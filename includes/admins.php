<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables

$options = dam_spam_get_options();

if ( $options['add_to_allow_list'] == 'Y' ) {
	dam_spam_sfs_check_admin();
}

if ( get_option( 'dam_spam_muswitch', 'N' ) == 'Y' ) {
	add_action( 'mu_rightnow_end', 'dam_spam_rightnow' );
	add_filter( 'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ), 'dam_spam_action_links' );
	add_filter( 'plugin_row_meta', 'dam_spam_action_links', 10, 2 );
	add_filter( 'wpmu_users_columns', 'dam_spam_sfs_ip_column_head' );
} else {
	add_action( 'admin_menu', 'dam_spam_admin_menu' );
	add_action( 'rightnow_end', 'dam_spam_rightnow' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dam_spam_action_links' );
	add_filter( 'manage_users_columns', 'dam_spam_sfs_ip_column_head' );
}

add_action( 'network_admin_menu', 'dam_spam_admin_menu' );
add_filter( 'comment_row_actions', 'dam_spam_row', 1, 2 );
add_action( 'wp_ajax_dam_spam_sfs_sub', 'dam_spam_sfs_handle_ajax_sub' );
add_action( 'wp_ajax_dam_spam_sfs_process', 'dam_spam_sfs_handle_process' );
add_action( 'manage_users_custom_column', 'dam_spam_sfs_ip_column', 10, 3 );

add_action( 'admin_enqueue_scripts', 'dam_spam_sfs_handle_ajax' );
function dam_spam_sfs_handle_ajax() {
	wp_enqueue_script( 'dam-spam', DAM_SPAM_URL . 'assets/js/admin.js', array(), DAM_SPAM_VERSION, false );
	wp_localize_script( 'dam-spam', 'damSpamAjax', array(
		'nonce' => wp_create_nonce( 'dam_spam_ajax' ),
		'func_nonces' => array(
			'add_white' => wp_create_nonce( 'dam_spam_process_add_white' ),
			'add_black' => wp_create_nonce( 'dam_spam_process_add_black' ),
			'delete_gcache' => wp_create_nonce( 'dam_spam_process_delete_gcache' ),
			'delete_bcache' => wp_create_nonce( 'dam_spam_process_delete_bcache' ),
			'delete_wl_row' => wp_create_nonce( 'dam_spam_process_delete_wl_row' ),
			'delete_wlip' => wp_create_nonce( 'dam_spam_process_delete_wlip' ),
			'delete_wlem' => wp_create_nonce( 'dam_spam_process_delete_wlem' ),
		),
	) );
}

function dam_spam_action_links( $links, $file ) {
	if ( strpos( $file, 'dam-spam' ) === false ) {
		return $links;
	}
	if ( DAM_SPAM_MU == 'Y' ) {
		$link = '<a href="' . admin_url( 'network/admin.php?page=dam-spam' ) . '">' . esc_html__( 'Settings', 'dam-spam' ) . '</a>';
	} else {
		$link = '<a href="' . admin_url( 'admin.php?page=dam-spam' ) . '">' . esc_html__( 'Settings', 'dam-spam' ) . '</a>';
	}
	$links[] = $link;
	return $links;
}

function dam_spam_rightnow() {
	$stats   = dam_spam_get_stats();
	extract( $stats );
	$options = dam_spam_get_options();
	if ( $spam_multisite_count > 0 ) {
		echo sprintf(
			// translators: %s is the number of spammers prevented
			'<p>' . esc_html__( 'Dam Spam has prevented %1$s spammers from registering or leaving comments.', 'dam-spam' ) . '</p>',
			'<strong>' . esc_html( $spam_multisite_count ) . '</strong>'
		);
	}
	if ( count( $allow_list_requests ) == 1 ) {
		// translators: %1$s and %2$s are opening/closing link tags for the allow list page
		echo '<p><strong>' . esc_html( count( $allow_list_requests ) ) . '</strong> ' . sprintf( wp_kses_post( __( 'user has been blocked and <a href="%s">requested</a> that you add them to the Allow List.', 'dam-spam' ) ), esc_url( admin_url( 'admin.php?page=dam-spam-allowed' ) ) ) . '</p>';
	} else if ( count( $allow_list_requests ) > 0 ) {
		// translators: %1$s and %2$s are opening/closing link tags for the allow list page
		echo '<p><strong>' . esc_html( count( $allow_list_requests ) ) . '</strong> ' . sprintf( wp_kses_post( __( 'users have been blocked and <a href="%s">requested</a> that you add them to the Allow List.', 'dam-spam' ) ), esc_url( admin_url( 'admin.php?page=dam-spam-allowed' ) ) ) . '</p>';
	}
}

function dam_spam_row( $actions, $comment ) {
	$options  = get_option( 'dam_spam_options' );
	$apikey   = $options['apikey'];
	$email	  = urlencode( $comment->comment_author_email );
	$ip	      = $comment->comment_author_ip;
	$action   = '';
	$whois	  = DAM_SPAM_URL . 'assets/images/whois.png';
	$who	  = "<a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://whois.domaintools.com/$ip\"><img src=\"$whois\" class=\"icon-action\"></a>";
	$stop	  = DAM_SPAM_URL . 'assets/images/stop.png';
	$hand	  = "<a title=\"" . esc_attr__( 'Check Stop Forum Spam', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://www.stopforumspam.com/search.php?q=$ip\"><img src=\"$stop\" class=\"icon-action\"> </a>";
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
	$evidence = trim( $evidence, "\r\n" );
	if ( strlen( $evidence ) > 128 ) {
		$evidence = substr( $evidence, 0, 125 ) . '...';
	}
	$evidence = urlencode( $evidence );
	$blog = 1;
	global $blog_id;
	if ( !isset( $blog_id ) || $blog_id != 1 ) {
		$blog = $blog_id;
	}
	$ajaxurl = admin_url( 'admin-ajax.php' );
	if ( !empty( $apikey ) ) {
		$target  = "";
		$href	 = "href=\"#\"";
		$onclick = "onclick=\"damSpamAjaxReportSpam(this,'$ID','$blog','$ajaxurl','$email','$ip','$uname');return false;\"";
	} else {
		$target  = " target=\"_blank\" ";
		$href	 = "href=\"https://www.stopforumspam.com/add.php?username=$uname&email=$email&ip_addr=$ip&evidence=$evidence&api_key=$apikey\" ";
		$onclick = '';
	}
	$action .= '<span title="' . esc_attr__( 'Add to Block List', 'dam-spam' ) . '" onclick="damSpamAjaxProcess(\'' . esc_js( $comment->comment_author_ip ) . '\',\'log\',\'add_black\',\'' . esc_js( $ajaxurl ) . '\');return false;"><img src="' . esc_url( DAM_SPAM_URL . 'assets/images/down.png' ) . '" class="icon-action"></span> ';
	$action .= '<span title="' . esc_attr__( 'Add to Allow List', 'dam-spam' ) . '" onclick="damSpamAjaxProcess(\'' . esc_js( $comment->comment_author_ip ) . '\',\'log\',\'add_white\',\'' . esc_js( $ajaxurl ) . '\');return false;"><img src="' . esc_url( DAM_SPAM_URL . 'assets/images/up.png' ) . '" class="icon-action"> | </span>';
	if ( !empty( $email ) ) {
		$action .= "<a $exst title=\"" . esc_attr__( 'Report to Stop Forum Spam', 'dam-spam' ) . "\" $target $href $onclick class='delete:the-comment-list:comment-$ID::delete=1 delete vim-d vim-destructive'>" . esc_html__( ' Report to SFS', 'dam-spam' ) . "</a>";
	}
	$actions['check_spam'] = $action;
	return $actions;
}

function dam_spam_ip_check() {
	$actionvalid = array( 'check_valid_ip', 'check_cloudflare' );
	foreach ( $actionvalid as $check ) {
		$reason = dam_spam_load( $check, $ip );
		if ( $reason !== false ) {
			return false;
		}
	}
	return true;
}

function dam_spam_sfs_handle_ajax_sub( $data ) {
	if ( !is_user_logged_in() ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dam_spam_ajax' ) ) {
		wp_die( esc_html__( 'Security check failed', 'dam-spam' ) );
	}
	$options = get_option( 'dam_spam_options' );
	if ( empty( $options ) ) {
		esc_html_e( ' No Options Set', 'dam-spam' );
		exit();
	}
	extract( $options );
	$comment_id = isset( $_POST['comment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['comment_id'] ) ) : '';
	if ( empty( $comment_id ) ) {
		esc_html_e( ' No Comment ID Found', 'dam-spam' );
		exit();
	}
	$blog = false;
	if ( isset( $_POST['blog_id'] ) && !empty( $_POST['blog_id'] ) && is_numeric( $_POST['blog_id'] ) ) {
		if ( function_exists( 'switch_to_blog' ) ) {
			switch_to_blog( ( int ) $_POST['blog_id'] );
			$blog = true;
		}
	}
	$comment = get_comment( $comment_id, ARRAY_A );
	if ( $comment_id == 'registration' ) {
		$comment = array(
			'comment_author_email' => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'comment_author' => isset( $_POST['user'] ) ? sanitize_user( wp_unslash( $_POST['user'] ) ) : '',
			'comment_author_ip' => isset( $_POST['ip'] ) ? sanitize_text_field( wp_unslash( $_POST['ip'] ) ) : '',
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
	if ( !empty( $ip_addr ) && !filter_var( $ip_addr, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		$ip_addr = '';
	}
	if ( empty( $ip_addr ) && empty( $email ) ) {
		esc_html_e( 'Cannot report: no valid IP address or email found for this comment.', 'dam-spam' );
		exit();
	}
	$content  = $comment['comment_content'];
	$evidence = $comment['comment_author_url'];
	if ( $blog ) {
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
	$evidence = trim( $evidence, "\r\n" );
	if ( strlen( $evidence ) > 128 ) {
		$evidence = substr( $evidence, 0, 125 ) . '...';
	}
	$evidence = urlencode( $evidence );
	if ( empty( $apikey ) ) {
		esc_html_e( 'Cannot Report Spam without API Key', 'dam-spam' );
		exit();
	}
	$hget = "https://www.stopforumspam.com/add.php?ip_addr=" . urlencode( $ip_addr ) . "&api_key=$apikey&email=$email&username=$uname&evidence=$evidence";
	$ret  = dam_spam_read_file( $hget );
	if ( stripos( $ret, 'data submitted successfully' ) !== false ) {
		esc_html_e( 'Spam reported successfully.', 'dam-spam' );
	} else if ( stripos( $ret, 'recent duplicate entry' ) !== false ) {
		esc_html_e( ' Recent Duplicate Entry ', 'dam-spam' );
	} else {
		echo esc_html__( 'Report failed: ', 'dam-spam' ) . esc_html( wp_strip_all_tags( $ret ) );
	}
	exit();
}

function dam_spam_sfs_get_urls( $content ) {
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

function dam_spam_sfs_handle_ajax_check( $data ) {
	if ( !dam_spam_ip_check() ) {
		esc_html_e( ' Not Enabled', 'dam-spam' );
		exit();
	}
	$query = "https://www.stopforumspam.com/api?ip=91.186.18.61";
	$check = '';
	$check = dam_spam_sfs_files( $query );
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

function dam_spam_sfs_handle_process( $data ) {
	if ( !is_user_logged_in() ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dam_spam_ajax' ) ) {
		wp_die( esc_html__( 'Security check failed', 'dam-spam' ) );
	}
	$func = isset( $_POST['func'] ) ? sanitize_text_field( wp_unslash( $_POST['func'] ) ) : '';
	if ( empty( $func ) ) {
		wp_die( esc_html__( 'Function not specified', 'dam-spam' ) );
	}
	$func_nonce = isset( $_POST['func_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['func_nonce'] ) ) : '';
	if ( !wp_verify_nonce( $func_nonce, 'dam_spam_process_' . $func ) ) {
		wp_die( esc_html__( 'Invalid function nonce', 'dam-spam' ) );
	}
	dam_spam_sfs_watch( $data );
}

// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in calling function dam_spam_sfs_handle_process()
function dam_spam_sfs_watch( $data ) {
	if ( !array_key_exists( 'func', $_POST ) ) {
		esc_html_e( ' Function Not Found', 'dam-spam' );
		exit();
	}
	$icons = dam_spam_get_icon_urls();
	extract( $icons );
	$ip        = isset( $_POST['ip'] ) ? sanitize_text_field( wp_unslash( $_POST['ip'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$container = isset( $_POST['cont'] ) ? sanitize_text_field( wp_unslash( $_POST['cont'] ) ) : '';
	$func      = isset( $_POST['func'] ) ? sanitize_text_field( wp_unslash( $_POST['func'] ) ) : '';
	$options   = dam_spam_get_options();
	$stats     = dam_spam_get_stats();
	$answer    = array();
	$allowed_html = dam_spam_get_ajax_allowed_html();
	switch ( $func ) {
		case 'delete_gcache':
			$answer = dam_spam_load( 'remove_good_cache', $ip, $stats, $options );
			$show = dam_spam_load( 'get_good_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'delete_bcache':
			$answer = dam_spam_load( 'remove_bad_cache', $ip, $stats, $options );
			$show = dam_spam_load( 'get_bad_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'add_black':
			if ( $container == 'badips' ) {
				dam_spam_load( 'remove_bad_cache', $ip, $stats, $options );
			} else if ( $container == 'goodips' ) {
				dam_spam_load( 'remove_good_cache', $ip, $stats, $options );
			} else {
				dam_spam_load( 'remove_bad_cache', $ip, $stats, $options );
				dam_spam_load( 'remove_good_cache', $ip, $stats, $options );
			}
			dam_spam_load( 'add_to_block_list', $ip, $stats, $options );
			break;
		case 'add_white':
			if ( $container == 'badips' ) {
				dam_spam_load( 'remove_bad_cache', $ip, $stats, $options );
			} else if ( $container == 'goodips' ) {
				dam_spam_load( 'remove_good_cache', $ip, $stats, $options );
			} else {
				dam_spam_load( 'remove_bad_cache', $ip, $stats, $options );
				dam_spam_load( 'remove_good_cache', $ip, $stats, $options );
			}
			dam_spam_load( 'add_to_allow_list', $ip, $stats, $options );
			break;
		case 'delete_wl_row':
			$answer = dam_spam_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
			break;
		case 'delete_wlip':
			$answer = dam_spam_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
			break;
		case 'delete_wlem':
			$answer = dam_spam_load( 'get_allow_requests', $ip, $stats, $options );
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
			$show = dam_spam_load( 'get_bad_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'goodips':
			$show = dam_spam_load( 'get_good_cache', 'x', $stats, $options );
			echo wp_kses( $show, $allowed_html );
			exit();
			break;
		case 'allow_list_request':
			$stats = dam_spam_get_stats();
			$answer = dam_spam_load( 'get_allow_requests', $ip, $stats, $options );
			echo wp_kses( $answer, $allowed_html );
			exit();
		default:
			// translators: %s is the missing container name
			printf( esc_html__( 'Something is missing: %s', 'dam-spam' ), esc_html( $container ) );
			exit();
	}
}
// phpcs:enable WordPress.Security.NonceVerification.Missing

function dam_spam_sfs_ip_column( $value, $column_name, $user_id ) {
	$icons = dam_spam_get_icon_urls();
	extract( $icons );
	if ( $column_name == 'signup_ip' ) {
		$signup_ip  = get_user_meta( $user_id, 'signup_ip', true );
		$signup_ip2 = $signup_ip;
		$ipline	 = '';
		if ( !empty( $signup_ip ) ) {
			$ipline = apply_filters( 'dam_spam_ip2link', $signup_ip2 );
			$user_info   = get_userdata( $user_id );
			$useremail   = urlencode( $user_info->user_email );
			$userurl	 = urlencode( $user_info->user_url );
			$username	 = urlencode( $user_info->user_login );
			$stopper	 = "<a title=\"" . esc_attr__( 'Check Stop Forum Spam', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://www.stopforumspam.com/search.php?q=$signup_ip\"><img src=\"$stop\" class=\"icon-action\"></a>";
			$honeysearch = "<a title=\"" . esc_attr__( 'Check Project HoneyPot', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://www.projecthoneypot.org/ip_$signup_ip\"><img src=\"$search\" class=\"icon-action\"></a>";
			$botsearch   = "<a title=\"" . esc_attr__( 'Check BotScout', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://botscout.com/search.htm?stype=q&sterm=$signup_ip\"><img src=\"$search\" class=\"icon-action\"></a>";
			$who		 = "<br><a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://whois.domaintools.com/$signup_ip\"><img src=\"$whois\" class=\"icon-action\"></a>";
			$action	     = " $who $stopper $honeysearch $botsearch";
			$options	 = dam_spam_get_options();
			$apikey	     = $options['apikey'];
			if ( !empty( $apikey ) ) {
				$ajaxurl = admin_url( 'admin-ajax.php' );
				$blog = 1;
				global $blog_id;
				if ( !isset( $blog_id ) || $blog_id != 1 ) {
					$blog = $blog_id;
				}
				$report  = "<a title=\"" . esc_attr__( 'Report to SFS', 'dam-spam' ) . "\" href=\"#\" onclick=\"damSpamAjaxReportSpam(this,'registration','" . esc_js( $blog ) . "','" . esc_js( $ajaxurl ) . "','" . esc_js( $useremail ) . "','" . esc_js( $signup_ip ) . "','" . esc_js( $username ) . "');return false;\"><img src=\"$stop\" class=\"icon-action\"></a>";
				$action .= $report;
			}
			return $ipline . $action;
		}
		return "";
	}
	return $value;
}

?>
