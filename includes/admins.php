<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

$options = ds_get_options();

if ( $options['add_to_allow_list'] == 'Y' ) {
	ds_sfs_check_admin();
}

if ( get_option( 'ds_muswitch', 'N' ) == 'Y' ) {
	add_action( 'mu_rightnow_end', 'ds_rightnow' );
	add_filter( 'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ), 'ds_action_links' );
	add_filter( 'plugin_row_meta', 'ds_action_links', 10, 2 );
	add_filter( 'wpmu_users_columns', 'ds_sfs_ip_column_head' );
} else {
	add_action( 'admin_menu', 'ds_admin_menu' );
	add_action( 'rightnow_end', 'ds_rightnow' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ds_action_links' );
	add_filter( 'manage_users_columns', 'ds_sfs_ip_column_head' );
}

add_action( 'network_admin_menu', 'ds_admin_menu' );
add_filter( 'comment_row_actions', 'ds_row', 1, 2 );
add_action( 'wp_ajax_sfs_sub', 'sfs_handle_ajax_sub' );
add_action( 'wp_ajax_sfs_process', 'sfs_handle_process' );
add_action( 'manage_users_custom_column', 'ds_sfs_ip_column', 10, 3 );
if ( function_exists( 'register_uninstall_hook' ) ) {
}

add_action( 'admin_enqueue_scripts', 'sfs_handle_ajax' );
function sfs_handle_ajax() {
	wp_enqueue_script( 'dam-spam', DS_PLUGIN_URL . 'assets/js/admin.js', array(), DS_VERSION, array( 'in_footer' => true ) );
}

function ds_action_links( $links, $file ) {
	if ( strpos( $file, 'dam-spam' ) === false ) {
		return $links;
	}
	if ( DS_MU == 'Y' ) {
		$link = '<a href="' . admin_url( 'network/admin.php?page=dam-spam' ) . '">' . esc_html__( 'Settings', 'dam-spam' ) . '</a>';
	} else {
		$link = '<a href="' . admin_url( 'admin.php?page=dam-spam' ) . '">' . esc_html__( 'Settings', 'dam-spam' ) . '</a>';
	}
	$links[] = $link;
	return $links;
}

function ds_rightnow() {
	$stats   = ds_get_stats();
	extract( $stats );
	$options = ds_get_options();
	if ( $spam_multisite_count > 0 ) {
		echo sprintf(
			// translators: %s is the number of spammers prevented
			'<p>' . esc_html__( 'Dam Spam has prevented %1$s spammers from registering or leaving comments.', 'dam-spam' ) . '</p>',
			'<strong>' . esc_html( $spam_multisite_count ) . '</strong>'
		);
	}
	if ( count( $allow_list_requests ) == 1 ) {
		// translators: %1$s and %2$s are opening/closing link tags for the allow list page
		echo '<p><strong>' . esc_html( count( $allow_list_requests ) ) . '</strong> ' . sprintf( wp_kses_post( __( 'user has been blocked and <a href="%s">requested</a> that you add them to the Allow List.', 'dam-spam' ) ), esc_url( admin_url( 'admin.php?page=ds-allowed' ) ) ) . '</p>';
	} else if ( count( $allow_list_requests ) > 0 ) {
		// translators: %1$s and %2$s are opening/closing link tags for the allow list page
		echo '<p><strong>' . esc_html( count( $allow_list_requests ) ) . '</strong> ' . sprintf( wp_kses_post( __( 'users have been blocked and <a href="%s">requested</a> that you add them to the Allow List.', 'dam-spam' ) ), esc_url( admin_url( 'admin.php?page=ds-allowed' ) ) ) . '</p>';
	}
}

function ds_row( $actions, $comment ) {
	$options  = get_option( 'ds_options' );
	$apikey   = $options['apikey'];
	$email	  = urlencode( $comment->comment_author_email );
	$ip	      = $comment->comment_author_ip;
	$action   = '';
	$whois	  = DS_PLUGIN_URL . 'assets/images/whois.png';
	$who	  = "<a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://whois.domaintools.com/$ip\"><img src=\"$whois\" class=\"icon-action\"></a>";
	$stop	  = DS_PLUGIN_URL . 'assets/images/stop.png';
	$hand	  = "<a title=\"" . esc_attr__( 'Check Stop Forum Spam', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://www.stopforumspam.com/search.php?q=$ip\"><img src=\"$stop\" class=\"icon-action\"> </a>";
	$action  .= " $who $hand";
	$email = urlencode( $comment->comment_author_email );
	if ( empty( $email ) ) {
		$actions['check_spam'] = $action;
		return $actions;
	}
	$ID	      = $comment->comment_ID;
	$exst	  = '';
	$uname	  = urlencode( $comment->comment_author );
	$content  = $comment->comment_content;
	$evidence = $comment->comment_author_url;
	if ( empty( $evidence ) ) {
		$evidence = '';
	}
	preg_match_all( '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $content, $post, PREG_PATTERN_ORDER );
	if ( is_array( $post ) && is_array( $post[1] ) ) {
		$urls1 = array_unique( $post[1] );
	} else {
		$urls1 = array();
	}
	preg_match_all( '/\[url=(.+)\]/iU', $content, $post, PREG_PATTERN_ORDER );
	if ( is_array( $post ) && is_array( $post[0] ) ) {
		$urls2 = array_unique( $post[0] );
	} else {
		$urls2 = array();
	}
	$urls3 = array_merge( $urls1, $urls2 );
	if ( is_array( $urls3 ) ) {
		$evidence .= "\r\n" . implode( "\r\n", $urls3 );
	}
	$evidence = urlencode( trim( $evidence, "\r\n" ) );
	if ( strlen( $evidence ) > 128 ) {
		$evidence = substr( $evidence, 0, 125 ) . '...';
	}
	$target  = " target=\"_blank\" ";
	$href	 = "href=\"https://www.stopforumspam.com/add.php?username=$uname&email=$email&ip_addr=$ip&evidence=$evidence&api_key=$apikey\" ";
	$onclick = '';
	$blog	 = 1;
	global $blog_id;
	if ( !isset( $blog_id ) || $blog_id != 1 ) {
		$blog = $blog_id;
	}
	$ajaxurl = admin_url( 'admin-ajax.php' );
	if ( !empty( $apikey ) ) {
		$href	 = "href=\"#\"";
		$onclick = "onclick=\"sfs_ajax_report_spam(this,'$ID','$blog','$ajaxurl');return false;\"";
	}
	$action .= '<span title="' . esc_attr__( 'Add to Block List', 'dam-spam' ) . '" onclick="sfs_ajax_process(\'' . esc_js( $comment->comment_author_ip ) . '\',\'log\',\'add_black\',\'' . esc_js( $ajaxurl ) . '\');return false;"><img src="' . esc_url( DS_PLUGIN_URL . 'assets/images/down.png' ) . '" class="icon-action"></span> ';
	$action .= '<span title="' . esc_attr__( 'Add to Allow List', 'dam-spam' ) . '" onclick="sfs_ajax_process(\'' . esc_js( $comment->comment_author_ip ) . '\',\'log\',\'add_white\',\'' . esc_js( $ajaxurl ) . '\');return false;"><img src="' . esc_url( DS_PLUGIN_URL . 'assets/images/up.png' ) . '" class="icon-action"> | </span>';
	if ( !empty( $email ) ) {
		$action .= "<a $exst title=\"" . esc_attr__( 'Report to Stop Forum Spam', 'dam-spam' ) . "\" $target $href $onclick class='delete:the-comment-list:comment-$ID::delete=1 delete vim-d vim-destructive'>" . esc_html__( ' Report to SFS', 'dam-spam' ) . "</a>";
	}
	$actions['check_spam'] = $action;
	return $actions;
}

function ipCheck() {
	$actionvalid = array( 'check_valid_ip', 'check_cloudflare' );
	foreach ( $actionvalid as $check ) {
		$reason = ds_load( $check, $ip );
		if ( $reason !== false ) {
			return false;
		}
	}
	return true;
}

function sfs_handle_ajax_sub( $data ) {
	if ( !is_user_logged_in() ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	$options = get_option( 'ds_options' );
	if ( empty( $options ) ) {
		esc_html_e( ' No Options Set', 'dam-spam' );
		exit();
	}
	extract( $options );
	$comment_id = isset( $_GET['comment_id'] ) ? urlencode( sanitize_text_field( wp_unslash( $_GET['comment_id'] ) ) ) : '';
	if ( empty( $comment_id ) ) {
		esc_html_e( ' No Comment ID Found', 'dam-spam' );
		exit();
	}
	$blog = '';
	if ( isset( $_GET['blog_id'] ) && !empty( $_GET['blog_id'] ) && is_numeric( $_GET['blog_id'] ) ) {
		if ( function_exists( 'switch_to_blog' ) ) {
			switch_to_blog( ( int ) $_GET['blog_id'] );
		}
	}
	$comment = get_comment( $comment_id, ARRAY_A );
	if ( $comment_id == 'registration' ) {
		$comment = array(
			'comment_author_email' => isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '',
			'comment_author' => isset( $_GET['user'] ) ? sanitize_user( wp_unslash( $_GET['user'] ) ) : '',
			'comment_author_ip' => isset( $_GET['ip'] ) ? sanitize_text_field( wp_unslash( $_GET['ip'] ) ) : '',
			'comment_content' => 'registration',
			'comment_author_url' => ''
		);
	} else {
		if ( empty( $comment ) ) {
			// translators: %s is the unrecognized function name
			printf( esc_html__( ' No Comment Found for %s', 'dam-spam' ), esc_html( $comment_id ) );
			exit();
		}
	}
	$email	  = urlencode( $comment['comment_author_email'] );
	$uname	  = urlencode( $comment['comment_author'] );
	$ip_addr  = $comment['comment_author_ip'];
	$content  = $comment['comment_content'];
	$evidence = $comment['comment_author_url'];
	if ( $blog != '' ) {
		if ( function_exists( 'restore_current_blog' ) ) {
			restore_current_blog();
		}
	}
	if ( empty( $evidence ) ) {
		$evidence = '';
	}
	preg_match_all( '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $content, $post, PREG_PATTERN_ORDER );
	$urls1 = array();
	$urls2 = array();
	if ( is_array( $post ) && is_array( $post[1] ) ) {
		$urls1 = array_unique( $post[1] );
	} else {
		$urls1 = array();
	}
	preg_match_all( '/\[url=(.+)\]/iU', $content, $post, PREG_PATTERN_ORDER );
	if ( is_array( $post ) && is_array( $post[0] ) ) {
		$urls2 = array_unique( $post[0] );
	} else {
		$urls2 = array();
	}
	$urls3 = array_merge( $urls1, $urls2 );
	if ( is_array( $urls3 ) ) {
		$evidence .= "\r\n" . implode( "\r\n", $urls3 );
	}
	$evidence = urlencode( trim( $evidence, "\r\n" ) );
	if ( strlen( $evidence ) > 128 ) {
		$evidence = substr( $evidence, 0, 125 ) . '...';
	}
	if ( empty( $apikey ) ) {
		esc_html_e( 'Cannot Report Spam without API Key', 'dam-spam' );
		exit();
	}
	$hget = "https://www.stopforumspam.com/add.php?ip_addr=$ip_addr&api_key=$apikey&email=$email&username=$uname&evidence=$evidence";
	$ret  = ds_read_file( $hget );
	if ( stripos( $ret, esc_html__( 'data submitted successfully', 'dam-spam' ) ) !== false ) {
		echo esc_html( $ret );
	} else if ( stripos( $ret, esc_html__( 'recent duplicate entry', 'dam-spam' ) ) !== false ) {
		esc_html_e( ' Recent Duplicate Entry ', 'dam-spam' );
	} else {
		esc_html_e( ' Returning from AJAX: ', 'dam-spam' ) . $hget . ' - ' . $ret;
	}
	exit();
}

function sfs_get_urls( $content ) {
	preg_match_all( '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $content, $post, PREG_PATTERN_ORDER );
	$urls1 = array();
	$urls2 = array();
	$urls3 = array();
	if ( is_array( $post ) && is_array( $post[1] ) ) {
		$urls1 = array_unique( $post[1] );
	} else {
		$urls1 = array();
	}
	preg_match_all( '/\[url=(.+)\]/iU', $content, $post, PREG_PATTERN_ORDER );
	if ( is_array( $post ) && is_array( $post[0] ) ) {
		$urls2 = array_unique( $post[0] );
	} else {
		$urls2 = array();
	}
	$urls3 = array_merge( $urls1, $urls2 );
	if ( !is_array( $urls3 ) ) {
		return array();
	}
	for ( $j = 0; $j < count( $urls3 ); $j ++ ) {
		$urls3[$j] = urlencode( $urls3[$j] );
	}
	return $urls3;
}

function sfs_handle_ajax_check( $data ) {
	if ( !ipCheck() ) {
		esc_html_e( ' Not Enabled', 'dam-spam' );
		exit();
	}
	$query = "https://www.stopforumspam.com/api?ip=91.186.18.61";
	$check = '';
	$check = ds_sfs_files( $query );
	if ( !empty( $check ) ) {
		$check = trim( $check );
		$check = trim( $check, '0' );
		if ( substr( $check, 0, 4 ) == "ERR:" ) {
			esc_html_e( ' Access to the Stop Forum Spam Database Shows Errors\r\n', 'dam-spam' );
			// translators: %s is the missing container name
			printf( esc_html__( ' Response Was: %s\r\n', 'dam-spam' ), esc_html( $check ) );
		}
		$n = strpos( $check, '<response success="true">' );
		if ( $n === false ) {
			esc_html_e( ' Access to the Stop Forum Spam Database is Not Working\r\n', 'dam-spam' );
			// translators: %s is the count of items in the list
			printf( esc_html__( ' Response was\r\n %s\r\n', 'dam-spam' ), esc_html( $check ) );
		} else {
			esc_html_e( ' Access to the Stop Forum Spam Database is Working', 'dam-spam' );
		}
	} else {
		esc_html_e( ' No Response from the Stop Forum Spam API Call\r\n', 'dam-spam' );
	}
	return;
}

function sfs_handle_process( $data ) {
	if ( !is_user_logged_in() ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	sfs_watch( $data );
}

function sfs_watch( $data ) {
	if ( !array_key_exists( 'func', $_GET ) ) {
		esc_html_e( ' Function Not Found', 'dam-spam' );
		exit();
	}
	$icons = ds_get_icon_urls();
	extract( $icons );
	$ip        = isset( $_GET['ip'] ) ? sanitize_text_field( wp_unslash( $_GET['ip'] ) ) : '';
	$email     = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
	$container = isset( $_GET['cont'] ) ? sanitize_text_field( wp_unslash( $_GET['cont'] ) ) : '';
	$func      = isset( $_GET['func'] ) ? sanitize_text_field( wp_unslash( $_GET['func'] ) ) : '';
	$options   = ds_get_options();
	$stats     = ds_get_stats();
	$answer    = array();
	$allowed_html = ds_get_ajax_allowed_html();
	switch ( $func ) {
		case 'delete_gcache':
			$answer = ds_load( 'remove_good_cache', $ip, $stats, $options );
			$show = ds_load( 'get_good_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'delete_bcache':
			$answer = ds_load( 'remove_bad_cache', $ip, $stats, $options );
			$show = ds_load( 'get_bad_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'add_black':
			if ( $container == 'badips' ) {
				ds_load( 'remove_bad_cache', $ip, $stats, $options );
			} else if ( $container == 'goodips' ) {
				ds_load( 'remove_good_cache', $ip, $stats, $options );
			} else {
				ds_load( 'remove_bad_cache', $ip, $stats, $options );
				ds_load( 'remove_good_cache', $ip, $stats, $options );
			}
			ds_load( 'add_to_block_list', $ip, $stats, $options );
			break;
		case 'add_white':
			if ( $container == 'badips' ) {
				ds_load( 'remove_bad_cache', $ip, $stats, $options );
			} else if ( $container == 'goodips' ) {
				ds_load( 'remove_good_cache', $ip, $stats, $options );
			} else {
				ds_load( 'remove_bad_cache', $ip, $stats, $options );
				ds_load( 'remove_good_cache', $ip, $stats, $options );
			}
			ds_load( 'add_to_allow_list', $ip, $stats, $options );
			break;
		case 'delete_wl_row':
			$answer = ds_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
			break;
		case 'delete_wlip':
			$answer = ds_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
			break;
		case 'delete_wlem':
			$answer = ds_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
			break;
		default:
			// translators: %s is the unrecognized function name
			printf( esc_html__( 'Unrecognized function "%s"', 'dam-spam' ), esc_html( $func ) );
			exit();
	}
	$ajaxurl  = admin_url( 'admin-ajax.php' );
	$cachedel = 'delete_gcache';
	switch ( $container ) {
		case 'badips':
			$show = ds_load( 'get_bad_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'goodips':
			$show = ds_load( 'get_good_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'allow_list_request':
			$stats = ds_get_stats();
			$answer = ds_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
		default:
			// translators: %s is the missing container name
			printf( esc_html__( 'Something is missing: %s', 'dam-spam' ), esc_html( $container ) );
			exit();
	}
}

function ds_sfs_ip_column( $value, $column_name, $user_id ) {
	$icons = ds_get_icon_urls();
	extract( $icons );
	if ( $column_name == 'signup_ip' ) {
		$signup_ip  = get_user_meta( $user_id, 'signup_ip', true );
		$signup_ip2 = $signup_ip;
		$ipline	 = '';
		if ( !empty( $signup_ip ) ) {
			$ipline = apply_filters( 'ip2link', $signup_ip2 );
			$user_info   = get_userdata( $user_id );
			$useremail   = urlencode( $user_info->user_email );
			$userurl	 = urlencode( $user_info->user_url );
			$username	 = $user_info->display_name;
			$stopper	 = "<a title=\"" . esc_attr__( 'Check Stop Forum Spam', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://www.stopforumspam.com/search.php?q=$signup_ip\"><img src=\"$stop\" class=\"icon-action\"></a>";
			$honeysearch = "<a title=\"" . esc_attr__( 'Check Project HoneyPot', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://www.projecthoneypot.org/ip_$signup_ip\"><img src=\"$search\" class=\"icon-action\"></a>";
			$botsearch   = "<a title=\"" . esc_attr__( 'Check BotScout', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://botscout.com/search.htm?stype=q&sterm=$signup_ip\"><img src=\"$search\" class=\"icon-action\"></a>";
			$who		 = "<br><a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://whois.domaintools.com/$signup_ip\"><img src=\"$whois\" class=\"icon-action\"></a>";
			$action	     = " $who $stopper $honeysearch $botsearch";
			$options	 = ds_get_options();
			$apikey	     = $options['apikey'];
			if ( !empty( $apikey ) ) {
				$report  = "<a title=\"" . esc_attr__( 'Report to SFS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://www.stopforumspam.com/add.php?username=$username&email=$useremail&ip_addr=$signup_ip&evidence=$userurl&api_key=$apikey\"><img src=\"$stop\" class=\"icon-action\"></a>";
				$action .= $report;
			}
			return $ipline . $action;
		}
		return "";
	}
	return $value;
}

?>
