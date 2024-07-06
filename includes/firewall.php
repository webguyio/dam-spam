<?php

function ds_cpt_init() {
	define( 'DS_BLOCKED', 1 );
	define( 'DS_AUTHORIZED', -1 );
	define( 'DS_INCOMING', 1 );
	define( 'DS_OUTGOING', -1 );
	if ( get_option( 'ds_license_status', false ) !== 'valid' ) {
		return;
	}
	ds_firewall_add_post_type();
	ds_firewall_incoming_requests();
	add_filter( 'manage_ds_firewall_posts_columns', 'ds_firewall_add_columns' );
	add_filter( 'manage_edit_ds_firewall_sortable_columns', 'ds_firewall_sortable_columns' );
	add_filter( 'manage_ds_firewall_posts_custom_column', 'ds_firewall_custom_column', 10, 2 );
	add_action( 'admin-print-styles-edit.php', function() { add_thickbox(); } );
	add_filter( 'views_edit_ds_firewall', 'remove_sub_action', 10, 1 );
	add_action( 'load-edit.php', 'ds_bulk_action' );
	add_filter( 'bulk_actions_edit_ds_firewall', '__return_empty_array' );
	// http request interseptor
	add_filter( 'pre_http_request', 'ds_inspect_request', 10, 3 );
	add_filter( 'http_api_debug', 'ds_log_response', 10, 5 );
	// for search and filter
	add_filter( 'request', 'ds_orderby_search_columns' );
	add_action( 'restrict_manage_posts', 'ds_new_filters' );
	add_filter( 'parse_query', 'ds_filter_query' );
}
add_action( 'init', 'ds_cpt_init' );

function ds_firewall_incoming_requests() {
	if ( is_user_logged_in() ) {
		return;
	}
	$url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://" .$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$ip  = $_SERVER['REMOTE_ADDR'];
	if ( !$host = parse_url( $url, PHP_URL_HOST ) or strpos( $_SERVER['SCRIPT_NAME'], "wp-cron.php" ) !== false ) {
		return;
	}
	if ( in_array( $ip, ds_get_options( 'user-ips' ) ) ) {
		ds_insert_post(
			array(
				'url'      => esc_url_raw( $url ),
				'code'     => '',
				'duration' => timer_stop( false, 2 ),
				'host'     => $host,
				'file'     => '',
				'line'     => '',
				'meta'     => '',
				'user-ip'  => $ip,
				'state'    => 1,
				'postdata' => ''
			)
		);
		http_response_code( 403 );
		exit;
	}
	ds_insert_post(
		array(
			'url'      => esc_url_raw( $url ),
			'code'     => '',
			'duration' => timer_stop( false, 2 ),
			'host'     => $host,
			'file'     => '',
			'line'     => '',
			'meta'     => '',
			'user-ip'  => $ip,
			'state'    => -1,
			'postdata' => ''
		)
	);
}

function remove_sub_action( $views ) {
	return array();
}

function ds_new_filters() {
	if ( !ds_current_screen( 'edit-ds-firewall' ) ) {
		return;
	}
	// no items?
	if ( !isset( $_GET['ds_firewall_state_filter'] ) && !_get_list_table( 'WP_Posts_List_Table' ) -> has_items() ) {
		return;
	}
	// filter value
	$filter = ( !isset( $_GET['ds_firewall_state_filter'] ) ? '' : ( int ) $_GET['ds_firewall_state_filter'] );
	$filter_request = ( !isset( $_GET['ds_firewall_type_filter'] ) ? '' : ( int ) $_GET['ds_firewall_type_filter'] );
	// filter dropdown
	echo sprintf(
		'<select name="ds_firewall_type_filter">%s%s%s</select>',
		'<option value="">' . esc_html__( 'All Requests', 'dam-spam' ) . '</option>',
		'<option value="' . DS_INCOMING . '" ' . selected( $filter_request, DS_INCOMING, false ) . '>' . esc_html__( 'Incoming', 'dam-spam' ). '</option>',
		'<option value="' . DS_OUTGOING . '" ' . selected( $filter_request, DS_OUTGOING, false ) . '>' . esc_html__( 'Outgoing', 'dam-spam' ) . '</option>'
	);

	echo sprintf(
		'<select name="ds_firewall_state_filter">%s%s%s</select>',
		'<option value="">' . esc_html__( 'All States', 'dam-spam' ) . '</option>',
		'<option value="' . DS_AUTHORIZED . '" ' . selected( $filter, DS_AUTHORIZED, false ) . '>' . esc_html__( 'Authorized', 'dam-spam' ). '</option>',
		'<option value="' . DS_BLOCKED . '" ' . selected( $filter, DS_BLOCKED, false ) . '>' . esc_html__( 'Blocked', 'dam-spam' ) . '</option>'
	);
	// empty protocol button
	if ( empty( $filter ) and empty( $filter_request ) ) {
		submit_button( esc_html__( 'Empty Protocol', 'dam-spam' ), 'apply', 'ds_firewall_delete_all', false );
	}
}

function ds_filter_query( $query ) {
	if ( !empty( $_GET['ds_firewall_state_filter'] ) ) {
      $query->set( 'meta_query', [array( 'key' => '_ds_firewall_state', 'value' => ( int ) $_GET['ds_firewall_state_filter'] )] );
	}
	if ( !empty( $_GET['ds_firewall_type_filter'] ) ) {
		$meta_filter = array();
		if ( $query->get( 'meta_query' ) ) {
			$meta_filter = $query->get( 'meta_query' );
		}
		if ( ( int ) $_GET['ds_firewall_type_filter'] === 1 ) {
			$meta_filter[] = array( 'key' => '_ds_firewall_user_ip' );
		}  else {
			$meta_filter[] = array( 'key' => '_ds_firewall_user_ip', 'compare' => 'NOT EXISTS' );
		}
		$query->set( 'meta_query', $meta_filter );
	}
}

function ds_bulk_action() {
	if ( !ds_current_screen( 'edit-ds-firewall' ) ) {
		return;
	}
	if ( !current_user_can( 'administrator' ) ) {
		return;
	}
	if ( !empty( $_GET['ds_firewall_delete_all'] ) ) {
		// check nonce
		check_admin_referer( 'bulk-posts' );
		// delete items
		ds_delete_all();
		// we're done
		wp_safe_redirect( add_query_arg( array( 'post_type' => 'ds-firewall' ), admin_url( 'edit.php' ) ) );
		exit();
	}
	if ( empty( $_GET['ds-action'] ) OR empty( $_GET['ds-type'] ) ) {
		return;
	}
	// set vars
	$action = $_GET['ds-action'];
	$type   = $_GET['ds-type'];
	// validate action and type
	if ( !in_array( $action, array( 'block', 'unblock' ) ) OR !in_array( $type, array( 'host', 'file', 'user-ip' ) ) ) {
		return;
	}
	// security check
	check_admin_referer( 'ds-firewall' );
	if ( empty( $_GET['id'] ) ) {
		return;
	}
	$item = ds_get_meta( $_GET['id'], $type );
	ds_update_options( $item , $type . 's', $action );
	wp_safe_redirect(
		add_query_arg( array( 'post_type' => 'ds-firewall', 'updated' => count( $ids ) * ( $action === 'unblock' ? -1 : 1 ), 'paged' => ds_get_pagenum() ), admin_url( 'edit.php' ) )
	);
	exit();
}

function ds_delete_all( $offset = 0 ) {
	$offset = ( int ) $offset;
	if ( $offset < 0 ) {
		return;
	}
	global $wpdb;
	$subquery = sprintf(
		"SELECT * FROM ( SELECT `ID` FROM `$wpdb->posts` WHERE `post_type` = 'ds-firewall' ORDER BY `ID` DESC LIMIT %d, 18446744073709551615 ) as t",
		$offset
	);
	// delete postmeta
	$wpdb->query(
		sprintf(
			"DELETE FROM `$wpdb->postmeta` WHERE `post_id` IN (%s)",
			$subquery
		)
	);
	// delete posts
	$wpdb->query(
		sprintf(
			"DELETE FROM `$wpdb->posts` WHERE `ID` IN (%s)",
			$subquery
		)
	);
}

function ds_delete_selected( $count = 0 ) {
	global $wpdb;
	$subquery = sprintf(
		"SELECT * FROM ( SELECT `ID` FROM `$wpdb->posts` WHERE `post_type` = 'ds-firewall' ORDER BY `ID` ASC LIMIT 0, %d  ) as t",
		$count
	);
	// delete postmeta
	$wpdb->query(
		sprintf(
			"DELETE FROM `$wpdb->postmeta` WHERE `post_id` IN (%s)",
			$subquery
		)
	);
	// delete posts
	$wpdb->query(
		sprintf(
			"DELETE FROM `$wpdb->posts` WHERE `ID` IN (%s)",
			$subquery
		)
	);
}

function ds_update_options( $item, $type, $action ) {
	$options = get_option( 'ds-firewall', array() );
	if ( !isset( $options[$type] ) ) {
		$options[$type] = array();
	}
	if ( !isset( $options[$type][$item] ) and $action == "block" ) {
		$options[$type][$item] = $item;
	}
	if ( isset( $options[$type][$item] ) and $action == "unblock" ) {
		unset( $options[$type][$item] );
	}
	update_option( 'ds-firewall', $options );	
}

function ds_get_options( $type ) {
	$options = get_option( 'ds-firewall', array() );
	if ( isset( $options[$type] ) ) {
		return $options[$type];
	}
	return $options;
}

function ds_firewall_add_post_type() {
	register_post_type(
		'ds-firewall',
		array(
			'label'  => 'Firewall',
			'labels' => array(
				'not_found' 		 => esc_html__( 'No items found. Future connections will be shown here.', 'dam-spam' ),
				'not_found_in_trash' => esc_html__( 'No items found in trash.', 'dam-spam' ),
				'search_items' 		 => esc_html__( 'Search in Destination', 'dam-spam' )
			),
			'public' 	   => false,
			'show_ui' 	   => true,
			'query_var'    => true,
			'hierarchical' => false,
			'capabilities' => array(
				'create_posts' => false,
				'delete_posts' => false
			),
			'show_in_menu' 		  => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true
		)
	);
	// admin only
	if ( !is_admin() ) {
		return;
	}
}

function ds_firewall_add_columns() {
	return ( array ) apply_filters(
		'ds_firewall_manage_columns',
		array(
			'url'      => esc_html__( 'Destination', 'dam-spam'),
			'file'     => esc_html__( 'File', 'dam-spam'),
			'state'    => esc_html__( 'State', 'dam-spam'),
			'code'     => esc_html__( 'Code', 'dam-spam'),
			'duration' => esc_html__( 'Duration', 'dam-spam'),
			'created'  => esc_html__( 'Time', 'dam-spam'),
			'postdata' => esc_html__( 'Data', 'dam-spam')
		)
	);
}

function ds_firewall_custom_column( $column, $post_id ) {
	$types = (array) apply_filters(
		'ds_firewall_custom_column',
		array(
			'url'      =>  'ds_html_url',
			'file'     =>  'ds_html_file',
			'state'    =>  'ds_html_state',
			'code'     =>  'ds_html_code',
			'duration' =>  'ds_html_duration',
			'created'  =>  'ds_html_created',
			'postdata' =>  'ds_html_postdata'
		)
	);
	// if type exists
	if ( !empty( $types[$column] ) ) {
		// callback
		$callback = $types[$column];
		// execute
		if ( is_callable( $callback ) ) {
			call_user_func(
				$callback,
				$post_id
			);
		}
	}
}

function ds_firewall_sortable_columns() {
	return ( array )apply_filters(
		'ds_firewall_sortable_columns',
		array(
			'url'     => 'url',
			'file'    => 'file',
			'state'   => 'state',
			'code'    => 'code',
			'created' => 'date'
		)
	);
}

function ds_html_url( $post_id ) {
	// init data
	$url  = ds_get_meta( $post_id, 'url' );
	$host = ds_get_meta( $post_id, 'host' );
	// already blacklisted?
	$blacklisted = in_array( $host, ds_get_options( 'hosts' ) );
	// print output

	if ( !empty( ds_get_meta( $post_id, 'user-ip' ) ) and empty( ds_get_meta( $post_id, 'file' ) ) ) {
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s</div>',
			$blacklisted,
			str_replace( $host, '<code>' . $host . '</code>', esc_url( $url ) )
		);
	} else {
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s<div class="row-actions">%s</div></div>',
			$blacklisted,
			str_replace( $host, '<code>' . $host . '</code>', esc_url( $url ) ),
			ds_action_link( $post_id, 'host', $blacklisted )
		);
	}
}

function ds_html_file( $post_id ) {
	// init data
	$file = ds_get_meta( $post_id, 'file' );
	$line = ds_get_meta( $post_id, 'line' );
	$meta = ds_get_meta( $post_id, 'meta' );
	$ip   = ds_get_meta( $post_id, 'user-ip' );
	if ( !is_array( $meta ) ) {
		$meta = array();
	}
	if ( !isset( $meta['type'] ) ) {
		$meta['type'] = 'WordPress';
	}
	if ( !isset( $meta['name'] ) ) {
		$meta['name'] = 'Core';
	}
	// already blacklisted?
	$blacklisted    = in_array( $file, ds_get_options( 'files' ) );
	$blacklisted_ip = in_array( $ip, ds_get_options( 'user-ips' ) );
	if ( !empty( ds_get_meta( $post_id, 'user-ip' ) ) ) {
		// print output
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s: %s<br><code>%s</code><div class="row-actions">%s</div></div>',
			$blacklisted_ip,
			'User',
			"IP",
			$ip,
			ds_action_link( $post_id, 'user-ip', $blacklisted_ip )
		);
	} else {
		// print output
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s: %s<br><code>/%s:%d</code><div class="row-actions">%s</div></div>',
			$blacklisted,
			$meta['type'],
			$meta['name'],
			$file,
			$line,
			ds_action_link(
				$post_id,
				'file',
				$blacklisted
			)
		);
	}
}

function ds_html_state( $post_id ) {
	// item state
	$state = ds_get_meta( $post_id, 'state' );
	// state values
	$states = array(
		DS_BLOCKED    => 'Blocked',
		DS_AUTHORIZED => 'Authorized'
	);
	// print the state
	echo sprintf( '<span class="%s">%s</span>', strtolower( $states[$state] ), esc_html__( $states[$state], 'dam-spam' ) );
	// colorize blocked item
	if ( $state == DS_BLOCKED ) {
		echo sprintf( '<style>#post-%1$d{background:rgba(248, 234, 232, 0.8)}#post-%1$d.alternate{background:#f8eae8}</style>', $post_id );
	}
}
	
function ds_html_code( $post_id ) {
	echo ds_get_meta( $post_id, 'code' );
}
	
function ds_html_duration( $post_id ) {
	if ( $duration = ds_get_meta( $post_id, 'duration' ) ) {
		echo sprintf( __( '%s seconds', 'dam-spam' ), $duration );
	}
}

function ds_html_created( $post_id ) {
	echo sprintf( __( '%s ago', 'dam-spam' ), human_time_diff( get_post_time( 'G', true, $post_id ) ) );
}

function ds_html_postdata( $post_id ) {
	// item post data
	$postdata = ds_get_meta( $post_id, 'postdata' );
	// empty data?
	if ( empty( $postdata ) ) {
		return;
	}
	// parse post data
	if ( !is_array( $postdata ) ) {
		wp_parse_str( $postdata, $postdata );
	}
	// empty array?
	if ( empty( $postdata ) ) {
		return;
	}
	// thickbox content start
	echo sprintf( '<div id="ds-firewall-thickbox-%d" class="ds-firewall-hidden"><pre>', $post_id );
	// post data
	print_r( $postdata );
	// thickbox content end
	echo '</pre></div>';
	// thickbox button
	echo sprintf( '<a href="#TB_inline?width=400&height=300&inlineId=ds-firewall-thickbox-%d" class="button thickbox">%s</a>', $post_id, esc_html__( 'Show', 'dam-spam') );
}

function ds_get_meta( $post_id, $key ) {
	if ( $value = get_post_meta( $post_id, '_ds-firewall_' . $key, true ) ) {
		return $value;
	}
	return get_post_meta( $post_id, $key, true );
}

function ds_action_link( $post_id, $type, $blacklisted = false ) {
	// link action
	$action = ( $blacklisted ? 'unblock' : 'block' );
	// block link
	return sprintf(
		'<a href="%s" class="%s">%s</a>',
		esc_url( wp_nonce_url( add_query_arg( array( 'id' => $post_id, 'paged' => ds_get_pagenum(), 'ds-type' => $type, 'ds-action' => $action, 'post_type' => 'ds-firewall' ), admin_url( 'edit.php' ) ), 'ds-firewall' ) ),
		$action,
		esc_html__( sprintf( '%s this %s', ucfirst( $action ), str_replace( '-', ' ', $type ) ), 'ds-firewall' )
	);
}

function ds_get_pagenum() {
	return ( empty( $GLOBALS['pagenum'] ) ? _get_list_table( 'WP_Posts_List_Table' ) -> get_pagenum() : $GLOBALS['pagenum'] );
}

function ds_debug_backtrace() {
	// reverse items
	$trace = array_reverse( debug_backtrace() );
	// loop items
    foreach( $trace as $index => $item ) {
    	if ( !empty( $item['function'] ) && strpos( $item['function'], 'wp_remote_' ) !== false ) {
    		// use prev item
    		if ( empty( $item['file'] ) ) {
    			$item = $trace[-- $index];
    		}
			// get file and line
    		if ( !empty( $item['file'] ) && ! empty( $item['line'] ) ) {
    			return $item;
    		}
    	}
    }
}

function ds_face_detect( $path ) {
	// default
	$meta = array( 'type' => 'WordPress', 'name' => 'Core' );
	// empty path
	if ( empty( $path ) ) {
		return $meta;
	}
	// search for plugin
	if ( $data = ds_localize_plugin( $path ) ) {
		return array(
			'type' => 'Plugin',
			'name' => $data['Name']
		);
	// search for theme
	} else if ( $data = ds_localize_theme( $path ) ) {
		return array(
			'type' => 'Theme',
			'name' => $data->get( 'Name' )
		);
	}
	return $meta;
}

function ds_localize_plugin( $path ) {
	// check path
	if ( strpos( $path, WP_PLUGIN_DIR ) === false ) {
		return false;
	}
	// reduce path
	$path = ltrim( str_replace( WP_PLUGIN_DIR, '', $path ), DIRECTORY_SEPARATOR );
	// get plugin folder
	$folder = substr( $path, 0, strpos( $path, DIRECTORY_SEPARATOR ) ) . DIRECTORY_SEPARATOR;
	// frontend
	if ( !function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	// all active plugins
	$plugins = get_plugins();
	// loop plugins
	foreach( $plugins as $path => $plugin ) {
		if ( strpos( $path, $folder ) === 0 ) {
			return $plugin;
		}
	}
}

function ds_localize_theme( $path ) {
	// check path
	if ( strpos( $path, get_theme_root() ) === false ) {
		return false;
	}
	// reduce path
	$path = ltrim( str_replace( get_theme_root(), '', $path ), DIRECTORY_SEPARATOR );
	// get theme folder
	$folder = substr( $path, 0, strpos( $path, DIRECTORY_SEPARATOR ) );
	// get theme
	$theme = wp_get_theme( $folder );
	// check and return theme
	if ( $theme->exists() ) {
		return $theme;
	}
	return false;
}

function ds_insert_post( $meta ) {
	// empty?
	if ( empty( $meta ) ) {
		return;
	}
	// limit requests entries
	$all_requests = wp_count_posts( 'ds-firewall' );
	if ( $all_requests->publish >= 10000 ) {
		ds_delete_selected( 1000 );
	}
	// create post
	$post_id = wp_insert_post(
		array(
			'post_status' => 'publish',
			'post_type'   => 'ds-firewall'
		)
	);
	// add meta values
	foreach( $meta as $key => $value ) {
		add_post_meta(
			$post_id,
			'_ds-firewall_' . $key,
			$value,
			true
		);
	}
	return $post_id;
}

function ds_get_postdata( $args ) {
	// no post data?
	if ( empty( $args['method'] ) OR $args['method'] !== 'POST' ) {
		return NULL;
	}
	// no body data?
	if ( empty( $args['body'] ) ) {
		return NULL;
	}
	return $args['body'];
}

// http request interseptor
function ds_inspect_request( $pre, $args, $url ) {
	// empty url
	if ( empty( $url ) ) {
		return $pre;
	}
	// invalid host
	if ( !$host = parse_url( $url, PHP_URL_HOST ) ) {
		return $pre;
	}
	// timer start
	timer_start();
	$track = ds_debug_backtrace();
	// no reference file found
	if ( empty( $track['file'] ) ) {
		return $pre;
	}
	// show your face, file
	$meta = ds_face_detect( $track['file'] );
	// init data
	$file = str_replace( ABSPATH, '', $track['file'] );
	$line = ( int ) $track['line'];
	// blocked item?
	if ( in_array( $host, ds_get_options( 'hosts' ) ) OR in_array( $file, ds_get_options( 'files' ) ) ) {
		return ds_insert_post(
			array(
				'url'      => esc_url_raw( $url ),
				'code'     => NULL,
				'host'     => $host,
				'file'     => $file,
				'line'     => $line,
				'meta'     => $meta,
				'state'    => 1,
				'postdata' => ds_get_postdata( $args )
			)
		);
	}
	return $pre;
}

function ds_log_response( $response, $type, $class, $args, $url ) {
	// only response type
	if ( $type !== 'response' ) {
		return false;
	}
	// empty url
	if ( empty( $url ) ) {
		return false;
	}
	// validate host
	if ( !$host = parse_url( $url, PHP_URL_HOST ) ) {
		return false;
	}
	// backtrace data
	$backtrace = ds_debug_backtrace();
	// no reference file found
	if ( empty( $backtrace['file'] ) ) {
		return false;
	}
	// show your face, file
	$meta = ds_face_detect( $backtrace['file'] );
	// extract backtrace data
	$file = str_replace( ABSPATH, '', $backtrace['file'] );
	$line = ( int ) $backtrace['line'];
	// response code
	$code = ( is_wp_error( $response ) ? -1 : wp_remote_retrieve_response_code( $response ) );
	// insert cpt
	ds_insert_post(
		array(
			'url'      => esc_url_raw( $url ),
			'code'     => $code,
			'duration' => timer_stop( false, 2 ),
			'host'     => $host,
			'file'     => $file,
			'line'     => $line,
			'meta'     => $meta,
			'state'    => -1,
			'postdata' => ds_get_postdata( $args )
		)
	);
}

function ds_orderby_search_columns( $vars ) {
	if ( !is_admin() ) {
		return $vars;
	}
	if ( !ds_current_screen( 'edit-ds-firewall' ) ) {
		return $vars;
	}
	// cpt search
	if ( !empty( $vars['s'] ) ) {
		add_filter( 'get_meta_sql', 'ds_modify_and_or' );
		$search_key = "_ds-firewall_url";
		if ( filter_var( $vars['s'], FILTER_VALIDATE_IP ) ) {
			$search_key = "_ds-firewall_user-ip";
		}
		// search in urls
		$meta_query = array(
			array(
				'key'     => $search_key,
				'value'   => $vars['s'],
				'compare' => 'LIKE'
			)
		);
		// combined with the filter
		if ( !empty( $_GET['ds-firewall_state_filter'] ) ) {
			$meta_query[] = array(
				'key'     => '_ds-firewall_state',
				'value'   => ( int ) $_GET['ds-firewall_state_filter'],
				'compare' => '=',
				'type'    => 'numeric'
			);
		}
		// merge attrs
		$vars = array_merge(
			$vars,
			array(
				'meta_query' => $meta_query
			)
		);
	}
	// cpt orderby
	if ( empty( $vars['orderby'] ) OR !in_array( $vars['orderby'], array( 'url', 'file', 'state', 'code' ) ) ) {
		return $vars;
	}
	// set var
	$orderby = $vars['orderby'];
	return array_merge(
		$vars,
		array(
            'meta_key' => '_ds-firewall_' . $orderby,
            'orderby'  => ( in_array( $orderby, array( 'code', 'state' ) ) ? 'meta_value_num' : 'meta_value' )
        )
     );
}

function ds_modify_and_or( $join_where ) {
	if ( !empty( $join_where['where'] ) ) {
		$join_where['where'] = str_replace( 'AND (', 'OR (', $join_where['where'] );
	}
	return $join_where;
}

function ds_current_screen( $id ) {
	$screen = get_current_screen();
	return ( is_object( $screen ) && $screen->id === $id );
}