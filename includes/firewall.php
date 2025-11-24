<?php
// phpcs:disable WordPress.Security.NonceVerification -- Firewall functionality processes request data for security monitoring
// phpcs:disable WordPress.DB.DirectDatabaseQuery -- Firewall logging requires direct DB access

function dam_spam_cpt_init() {
	define( 'DAM_SPAM_BLOCKED', 1 );
	define( 'DAM_SPAM_AUTHORIZED', -1 );
	define( 'DAM_SPAM_INCOMING', 1 );
	define( 'DAM_SPAM_OUTGOING', -1 );
	if ( get_option( 'dam_spam_license_status', false ) !== 'valid' ) {
		return;
	}
	dam_spam_firewall_add_post_type();
	dam_spam_firewall_incoming_requests();
	add_filter( 'manage_dam_spam_firewall_posts_columns', 'dam_spam_firewall_add_columns' );
	add_filter( 'manage_edit_dam_spam_firewall_sortable_columns', 'dam_spam_firewall_sortable_columns' );
	add_filter( 'manage_dam_spam_firewall_posts_custom_column', 'dam_spam_firewall_custom_column', 10, 2 );
	add_action( 'admin-print-styles-edit.php', function() { add_thickbox(); } );
	add_filter( 'views_edit_dam_spam_firewall', 'dam_spam_remove_sub_action', 10, 1 );
	add_action( 'load-edit.php', 'dam_spam_bulk_action' );
	add_filter( 'bulk_actions_edit_dam_spam_firewall', '__return_empty_array' );
	add_filter( 'pre_http_request', 'dam_spam_inspect_request', 10, 3 );
	add_filter( 'http_api_debug', 'dam_spam_log_response', 10, 5 );
	add_filter( 'request', 'dam_spam_orderby_search_columns' );
	add_action( 'restrict_manage_posts', 'dam_spam_new_filters' );
	add_filter( 'parse_query', 'dam_spam_filter_query' );
}
add_action( 'init', 'dam_spam_cpt_init' );

function dam_spam_firewall_incoming_requests() {
	if ( is_user_logged_in() ) {
		return;
	}
	$https = isset( $_SERVER['HTTPS'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) : '';
	$http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ) : '';
	$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( empty( $http_host ) || empty( $request_uri ) || empty( $remote_addr ) ) {
		return;
	}
	$url = ( $https === 'on' ? 'https' : 'http' ) . '://' . $http_host . $request_uri;
	$ip = $remote_addr;
	if ( !$host = wp_parse_url( $url, PHP_URL_HOST ) or strpos( $script_name, 'wp-cron.php' ) !== false ) {
		return;
	}
	if ( in_array( $ip, dam_spam_get_options( 'user-ips' ) ) ) {
		dam_spam_insert_post(
			array(
				'url' => esc_url_raw( $url ),
				'code' => '',
				'duration' => timer_stop( false, 2 ),
				'host' => sanitize_text_field( $host ),
				'file' => '',
				'line' => '',
				'meta' => '',
				'user-ip' => sanitize_text_field( $ip ),
				'state' => 1,
				'postdata' => ''
			)
		);
		http_response_code( 403 );
		exit;
	}
	dam_spam_insert_post(
		array(
			'url' => esc_url_raw( $url ),
			'code' => '',
			'duration' => timer_stop( false, 2 ),
			'host' => sanitize_text_field( $host ),
			'file' => '',
			'line' => '',
			'meta' => '',
			'user-ip' => sanitize_text_field( $ip ),
			'state' => -1,
			'postdata' => ''
		)
	);
}

function dam_spam_remove_sub_action( $views ) {
	return array();
}

function dam_spam_new_filters() {
	if ( !dam_spam_current_screen( 'edit-dam-spam-firewall' ) ) {
		return;
	}
	if ( !isset( $_GET['dam_spam_firewall_state_filter'] ) && !_get_list_table( 'WP_Posts_List_Table' ) -> has_items() ) {
		return;
	}
	$filter = ( !isset( $_GET['dam_spam_firewall_state_filter'] ) ? '' : absint( $_GET['dam_spam_firewall_state_filter'] ) );
	$filter_request = ( !isset( $_GET['dam_spam_firewall_type_filter'] ) ? '' : absint( $_GET['dam_spam_firewall_type_filter'] ) );
	echo sprintf(
		'<select name="dam_spam_firewall_type_filter">%s%s%s</select>',
		'<option value="">' . esc_html__( 'All Requests', 'dam-spam' ) . '</option>',
		'<option value="' . esc_attr( DAM_SPAM_INCOMING ) . '" ' . selected( $filter_request, DAM_SPAM_INCOMING, false ) . '>' . esc_html__( 'Incoming', 'dam-spam' ). '</option>',
		'<option value="' . esc_attr( DAM_SPAM_OUTGOING ) . '" ' . selected( $filter_request, DAM_SPAM_OUTGOING, false ) . '>' . esc_html__( 'Outgoing', 'dam-spam' ) . '</option>'
	);
	echo sprintf(
		'<select name="dam_spam_firewall_state_filter">%s%s%s</select>',
		'<option value="">' . esc_html__( 'All States', 'dam-spam' ) . '</option>',
		'<option value="' . esc_attr( DAM_SPAM_AUTHORIZED ) . '" ' . selected( $filter, DAM_SPAM_AUTHORIZED, false ) . '>' . esc_html__( 'Authorized', 'dam-spam' ). '</option>',
		'<option value="' . esc_attr( DAM_SPAM_BLOCKED ) . '" ' . selected( $filter, DAM_SPAM_BLOCKED, false ) . '>' . esc_html__( 'Blocked', 'dam-spam' ) . '</option>'
	);
	if ( empty( $filter ) and empty( $filter_request ) ) {
		submit_button( esc_html__( 'Empty Protocol', 'dam-spam' ), 'apply', 'dam_spam_firewall_delete_all', false );
	}
}

function dam_spam_filter_query( $query ) {
	if ( !empty( $_GET['dam_spam_firewall_state_filter'] ) ) {
      $query->set( 'meta_query', [array( 'key' => '_dam_spam_firewall_state', 'value' => ( int ) $_GET['dam_spam_firewall_state_filter'] )] );
	}
	if ( !empty( $_GET['dam_spam_firewall_type_filter'] ) ) {
		$meta_filter = array();
		if ( $query->get( 'meta_query' ) ) {
			$meta_filter = $query->get( 'meta_query' );
		}
		if ( ( int ) $_GET['dam_spam_firewall_type_filter'] === 1 ) {
			$meta_filter[] = array( 'key' => '_dam_spam_firewall_user_ip' );
		}  else {
			$meta_filter[] = array( 'key' => '_dam_spam_firewall_user_ip', 'compare' => 'NOT EXISTS' );
		}
		$query->set( 'meta_query', $meta_filter );
	}
}

function dam_spam_bulk_action() {
	if ( !dam_spam_current_screen( 'edit-dam-spam-firewall' ) ) {
		return;
	}
	if ( !current_user_can( 'administrator' ) ) {
		return;
	}
	if ( !empty( $_GET['dam_spam_firewall_delete_all'] ) ) {
		check_admin_referer( 'bulk-posts' );
		dam_spam_delete_all();
		wp_safe_redirect( add_query_arg( array( 'post_type' => 'dam-spam-firewall' ), admin_url( 'edit.php' ) ) );
		exit();
	}
	if ( empty( $_GET['dam-spam-action'] ) OR empty( $_GET['dam-spam-type'] ) ) {
		return;
	}
	$action = sanitize_text_field( wp_unslash( $_GET['dam-spam-action'] ) );
	$type   = sanitize_text_field( wp_unslash( $_GET['dam-spam-type'] ) );
	if ( !in_array( $action, array( 'block', 'unblock' ) ) OR !in_array( $type, array( 'host', 'file', 'user-ip' ) ) ) {
		return;
	}
	check_admin_referer( 'dam-spam-firewall' );
	if ( empty( $_GET['id'] ) ) {
		return;
	}
	$item = dam_spam_get_meta( sanitize_text_field( wp_unslash( $_GET['id'] ) ), $type );
	dam_spam_update_options( $item , $type . 's', $action );
	wp_safe_redirect(
		add_query_arg( array( 'post_type' => 'dam-spam-firewall', 'updated' => count( $ids ) * ( $action === 'unblock' ? -1 : 1 ), 'paged' => dam_spam_get_pagenum() ), admin_url( 'edit.php' ) )
	);
	exit();
}

function dam_spam_delete_all( $offset = 0 ) {
	$offset = absint( $offset );
	if ( $offset < 0 ) {
		return;
	}
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"DELETE pm FROM `$wpdb->postmeta` pm
			INNER JOIN (
				SELECT `ID` FROM `$wpdb->posts`
				WHERE `post_type` = 'dam-spam-firewall'
				ORDER BY `ID` DESC
				LIMIT %d, 18446744073709551615
			) as t ON pm.post_id = t.ID",
			$offset
		)
	);
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM `$wpdb->posts`
			WHERE `post_type` = 'dam-spam-firewall'
			ORDER BY `ID` DESC
			LIMIT %d, 18446744073709551615",
			$offset
		)
	);
}

function dam_spam_delete_selected( $count = 0 ) {
	$count = absint( $count );
	if ( $count <= 0 ) {
		return;
	}
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"DELETE pm FROM `$wpdb->postmeta` pm
			INNER JOIN (
				SELECT `ID` FROM `$wpdb->posts`
				WHERE `post_type` = 'dam-spam-firewall'
				ORDER BY `ID` ASC
				LIMIT 0, %d
			) as t ON pm.post_id = t.ID",
			$count
		)
	);
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM `$wpdb->posts`
			WHERE `post_type` = 'dam-spam-firewall'
			ORDER BY `ID` ASC
			LIMIT 0, %d",
			$count
		)
	);
}

function dam_spam_update_options( $item, $type, $action ) {
	$options = get_option( 'dam-spam-firewall', array() );
	if ( !isset( $options[$type] ) ) {
		$options[$type] = array();
	}
	if ( !isset( $options[$type][$item] ) and $action == "block" ) {
		$options[$type][$item] = $item;
	}
	if ( isset( $options[$type][$item] ) and $action == "unblock" ) {
		unset( $options[$type][$item] );
	}
	update_option( 'dam-spam-firewall', $options );	
}

function dam_spam_get_options( $type ) {
	$options = get_option( 'dam-spam-firewall', array() );
	if ( isset( $options[$type] ) ) {
		return $options[$type];
	}
	return $options;
}

function dam_spam_firewall_add_post_type() {
	register_post_type(
		'dam-spam-firewall',
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
	if ( !is_admin() ) {
		return;
	}
}

function dam_spam_firewall_add_columns() {
	return ( array ) apply_filters(
		'dam_spam_firewall_manage_columns',
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

function dam_spam_firewall_custom_column( $column, $post_id ) {
	$types = (array) apply_filters(
		'dam_spam_firewall_custom_column',
		array(
			'url'      =>  'dam_spam_html_url',
			'file'     =>  'dam_spam_html_file',
			'state'    =>  'dam_spam_html_state',
			'code'     =>  'dam_spam_html_code',
			'duration' =>  'dam_spam_html_duration',
			'created'  =>  'dam_spam_html_created',
			'postdata' =>  'dam_spam_html_postdata'
		)
	);
	if ( !empty( $types[$column] ) ) {
		$callback = $types[$column];
		if ( is_callable( $callback ) ) {
			call_user_func(
				$callback,
				$post_id
			);
		}
	}
}

function dam_spam_firewall_sortable_columns() {
	return ( array )apply_filters(
		'dam_spam_firewall_sortable_columns',
		array(
			'url'     => 'url',
			'file'    => 'file',
			'state'   => 'state',
			'code'    => 'code',
			'created' => 'date'
		)
	);
}

function dam_spam_html_url( $post_id ) {
	$url  = dam_spam_get_meta( $post_id, 'url' );
	$host = dam_spam_get_meta( $post_id, 'host' );
	$blacklisted = in_array( $host, dam_spam_get_options( 'hosts' ) );
	if ( !empty( dam_spam_get_meta( $post_id, 'user-ip' ) ) and empty( dam_spam_get_meta( $post_id, 'file' ) ) ) {
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s</div>',
			esc_html( $blacklisted ),
			wp_kses_post( str_replace( $host, '<code>' . esc_html( $host ) . '</code>', esc_url( $url ) ) )
		);
	} else {
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s<div class="row-actions">%s</div></div>',
			esc_html( $blacklisted ),
			wp_kses_post( str_replace( $host, '<code>' . esc_html( $host ) . '</code>', esc_url( $url ) ) ),
			wp_kses_post( dam_spam_action_link( $post_id, 'host', $blacklisted ) )
		);
	}
}

function dam_spam_html_file( $post_id ) {
	$file = dam_spam_get_meta( $post_id, 'file' );
	$line = dam_spam_get_meta( $post_id, 'line' );
	$meta = dam_spam_get_meta( $post_id, 'meta' );
	$ip   = dam_spam_get_meta( $post_id, 'user-ip' );
	if ( !is_array( $meta ) ) {
		$meta = array();
	}
	if ( !isset( $meta['type'] ) ) {
		$meta['type'] = 'WordPress';
	}
	if ( !isset( $meta['name'] ) ) {
		$meta['name'] = 'Core';
	}
	$blacklisted    = in_array( $file, dam_spam_get_options( 'files' ) );
	$blacklisted_ip = in_array( $ip, dam_spam_get_options( 'user-ips' ) );
	if ( !empty( dam_spam_get_meta( $post_id, 'user-ip' ) ) ) {
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s: %s<br><code>%s</code><div class="row-actions">%s</div></div>',
			esc_html( $blacklisted_ip ),
			'User',
			"IP",
			esc_html( $ip ),
			wp_kses_post( dam_spam_action_link( $post_id, 'user-ip', $blacklisted_ip ) )
		);
	} else {
		echo sprintf(
			'<div><p class="label blacklisted_%d"></p>%s: %s<br><code>/%s:%d</code><div class="row-actions">%s</div></div>',
			esc_html( $blacklisted ),
			esc_html( $meta['type'] ),
			esc_html( $meta['name'] ),
			esc_html( $file ),
			esc_html( $line ),
			wp_kses_post( dam_spam_action_link( $post_id, 'file', $blacklisted ) )
		);
	}
}

function dam_spam_html_state( $post_id ) {
	$state = dam_spam_get_meta( $post_id, 'state' );
	$states = array(
		DAM_SPAM_BLOCKED    => 'Blocked',
		DAM_SPAM_AUTHORIZED => 'Authorized'
	);
	print '<span class="' . esc_html( strtolower( $states[$state] ) ) . '">' . esc_html( $states[$state] ) . '</span>';
	if ( $state == DAM_SPAM_BLOCKED ) {
		printf( '<style>#post-%1$d{background:rgba(248, 234, 232, 0.8)}#post-%1$d.alternate{background:#f8eae8}</style>', esc_html( $post_id ) );
	}
}
	
function dam_spam_html_code( $post_id ) {
	echo esc_html( dam_spam_get_meta( $post_id, 'code' ) );
}
	
function dam_spam_html_duration( $post_id ) {
	if ( $duration = dam_spam_get_meta( $post_id, 'duration' ) ) {
		// translators: %s is the custom post type name that's missing
		printf( esc_html__( '%s seconds', 'dam-spam' ), esc_html( $duration ) );
	}
}

function dam_spam_html_created( $post_id ) {
	// translators: %s is the configuration error details
	printf( esc_html__( '%s ago', 'dam-spam' ), esc_html( human_time_diff( get_post_time( 'G', true, $post_id ) ) ) );
}

function dam_spam_html_postdata( $post_id ) {
	$postdata = dam_spam_get_meta( $post_id, 'postdata' );
	if ( empty( $postdata ) ) {
		return;
	}
	if ( !is_array( $postdata ) ) {
		wp_parse_str( $postdata, $postdata );
	}
	if ( empty( $postdata ) ) {
		return;
	}
	printf( '<div id="dam-spam-firewall-thickbox-%d" class="dam-spam-firewall-hidden"><pre>', absint( $post_id ) );
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Debug display for firewall admin interface
	echo esc_html( print_r( $postdata, true ) );
	echo '</pre></div>';
	printf( '<a href="#TB_inline?width=400&height=300&inlineId=dam-spam-firewall-thickbox-%d" class="button thickbox">%s</a>', absint( $post_id ), esc_html__( 'Show', 'dam-spam' ) );
}

function dam_spam_get_meta( $post_id, $key ) {
	if ( $value = get_post_meta( $post_id, '_dam-spam-firewall_' . $key, true ) ) {
		return $value;
	}
	return get_post_meta( $post_id, $key, true );
}

function dam_spam_action_link( $post_id, $type, $blacklisted = false ) {
	$action = ( $blacklisted ? 'unblock' : 'block' );
	return sprintf(
		'<a href="%s" class="%s">%s</a>',
		esc_url( wp_nonce_url( add_query_arg( array( 'id' => $post_id, 'paged' => dam_spam_get_pagenum(), 'dam-spam-type' => $type, 'dam-spam-action' => $action, 'post_type' => 'dam-spam-firewall' ), admin_url( 'edit.php' ) ), 'dam-spam-firewall' ) ),
		$action,
		// translators: %s is the type of item that could not be found
		sprintf( esc_html__( '%1$s this %2$s', 'dam-spam' ), ucfirst( $action ), str_replace( '-', ' ', $type ) )
	);
}

function dam_spam_get_pagenum() {
	return ( empty( $GLOBALS['pagenum'] ) ? _get_list_table( 'WP_Posts_List_Table' ) -> get_pagenum() : $GLOBALS['pagenum'] );
}

function dam_spam_debug_backtrace() {
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- Firewall tracking functionality
	$trace = array_reverse( debug_backtrace() );
    foreach( $trace as $index => $item ) {
    	if ( !empty( $item['function'] ) && strpos( $item['function'], 'wp_remote_' ) !== false ) {
    		if ( empty( $item['file'] ) ) {
    			$item = $trace[-- $index];
    		}
    		if ( !empty( $item['file'] ) && ! empty( $item['line'] ) ) {
    			return $item;
    		}
    	}
    }
}

function dam_spam_face_detect( $path ) {
	$meta = array( 'type' => 'WordPress', 'name' => 'Core' );
	if ( empty( $path ) ) {
		return $meta;
	}
	if ( $data = dam_spam_localize_plugin( $path ) ) {
		return array(
			'type' => 'Plugin',
			'name' => $data['Name']
		);
	} else if ( $data = dam_spam_localize_theme( $path ) ) {
		return array(
			'type' => 'Theme',
			'name' => $data->get( 'Name' )
		);
	}
	return $meta;
}

function dam_spam_localize_plugin( $path ) {
	if ( strpos( $path, WP_PLUGIN_DIR ) === false ) {
		return false;
	}
	$path = ltrim( str_replace( WP_PLUGIN_DIR, '', $path ), DIRECTORY_SEPARATOR );
	$folder = substr( $path, 0, strpos( $path, DIRECTORY_SEPARATOR ) ) . DIRECTORY_SEPARATOR;
	if ( !function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$plugins = get_plugins();
	foreach( $plugins as $path => $plugin ) {
		if ( strpos( $path, $folder ) === 0 ) {
			return $plugin;
		}
	}
}

function dam_spam_localize_theme( $path ) {
	if ( strpos( $path, get_theme_root() ) === false ) {
		return false;
	}
	$path = ltrim( str_replace( get_theme_root(), '', $path ), DIRECTORY_SEPARATOR );
	$folder = substr( $path, 0, strpos( $path, DIRECTORY_SEPARATOR ) );
	$theme = wp_get_theme( $folder );
	if ( $theme->exists() ) {
		return $theme;
	}
	return false;
}

function dam_spam_insert_post( $meta ) {
	if ( empty( $meta ) ) {
		return;
	}
	$all_requests = wp_count_posts( 'dam-spam-firewall' );
	if ( $all_requests->publish >= 10000 ) {
		dam_spam_delete_selected( 1000 );
	}
	$post_id = wp_insert_post(
		array(
			'post_status' => 'publish',
			'post_type'   => 'dam-spam-firewall'
		)
	);
	foreach( $meta as $key => $value ) {
		add_post_meta(
			$post_id,
			'_dam-spam-firewall_' . $key,
			$value,
			true
		);
	}
	return $post_id;
}

function dam_spam_get_postdata( $args ) {
	if ( empty( $args['method'] ) OR $args['method'] !== 'POST' ) {
		return NULL;
	}
	if ( empty( $args['body'] ) ) {
		return NULL;
	}
	return $args['body'];
}

function dam_spam_inspect_request( $pre, $args, $url ) {
	if ( empty( $url ) ) {
		return $pre;
	}
	if ( !$host = wp_parse_url( $url, PHP_URL_HOST ) ) {
		return $pre;
	}
	timer_start();
	$track = dam_spam_debug_backtrace();
	if ( empty( $track['file'] ) ) {
		return $pre;
	}
	$meta = dam_spam_face_detect( $track['file'] );
	$file = str_replace( ABSPATH, '', $track['file'] );
	$line = ( int ) $track['line'];
	if ( in_array( $host, dam_spam_get_options( 'hosts' ) ) OR in_array( $file, dam_spam_get_options( 'files' ) ) ) {
		return dam_spam_insert_post(
			array(
				'url'      => esc_url_raw( $url ),
				'code'     => NULL,
				'host'     => $host,
				'file'     => $file,
				'line'     => $line,
				'meta'     => $meta,
				'state'    => 1,
				'postdata' => dam_spam_get_postdata( $args )
			)
		);
	}
	return $pre;
}

function dam_spam_log_response( $response, $type, $class, $args, $url ) {
	if ( $type !== 'response' ) {
		return false;
	}
	if ( empty( $url ) ) {
		return false;
	}
	if ( !$host = wp_parse_url( $url, PHP_URL_HOST ) ) {
		return false;
	}
	$backtrace = dam_spam_debug_backtrace();
	if ( empty( $backtrace['file'] ) ) {
		return false;
	}
	$meta = dam_spam_face_detect( $backtrace['file'] );
	$file = str_replace( ABSPATH, '', $backtrace['file'] );
	$line = ( int ) $backtrace['line'];
	$code = ( is_wp_error( $response ) ? -1 : wp_remote_retrieve_response_code( $response ) );
	dam_spam_insert_post(
		array(
			'url'      => esc_url_raw( $url ),
			'code'     => $code,
			'duration' => timer_stop( false, 2 ),
			'host'     => $host,
			'file'     => $file,
			'line'     => $line,
			'meta'     => $meta,
			'state'    => -1,
			'postdata' => dam_spam_get_postdata( $args )
		)
	);
}

function dam_spam_orderby_search_columns( $vars ) {
	if ( !is_admin() ) {
		return $vars;
	}
	if ( !dam_spam_current_screen( 'edit-dam-spam-firewall' ) ) {
		return $vars;
	}
	if ( !empty( $vars['s'] ) ) {
		add_filter( 'get_meta_sql', 'dam_spam_modify_and_or' );
		$search_key = "_dam-spam-firewall_url";
		if ( filter_var( $vars['s'], FILTER_VALIDATE_IP ) ) {
			$search_key = "_dam-spam-firewall_user-ip";
		}
		$meta_query = array(
			array(
				'key'     => $search_key,
				'value'   => $vars['s'],
				'compare' => 'LIKE'
			)
		);
		if ( !empty( $_GET['dam-spam-firewall_state_filter'] ) ) {
			$meta_query[] = array(
				'key'     => '_dam-spam-firewall_state',
				'value'   => ( int ) $_GET['dam-spam-firewall_state_filter'],
				'compare' => '=',
				'type'    => 'numeric'
			);
		}
		$vars = array_merge(
			$vars,
			array(
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Necessary for firewall filtering
				'meta_query' => $meta_query
			)
		);
	}
	if ( empty( $vars['orderby'] ) OR !in_array( $vars['orderby'], array( 'url', 'file', 'state', 'code' ) ) ) {
		return $vars;
	}
	$orderby = $vars['orderby'];
	return array_merge(
		$vars,
		array(
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Necessary for firewall sorting
            'meta_key' => '_dam-spam-firewall_' . $orderby,
            'orderby'  => ( in_array( $orderby, array( 'code', 'state' ) ) ? 'meta_value_num' : 'meta_value' )
        )
     );
}

function dam_spam_modify_and_or( $join_where ) {
	if ( !empty( $join_where['where'] ) ) {
		$join_where['where'] = str_replace( 'AND (', 'OR (', $join_where['where'] );
	}
	return $join_where;
}

function dam_spam_current_screen( $id ) {
	$screen = get_current_screen();
	return ( is_object( $screen ) && $screen->id === $id );
}