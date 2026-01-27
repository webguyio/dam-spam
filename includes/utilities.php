<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- Utility functions require direct DB access

function dam_spam_read_file( $f, $method = 'GET' ) {
	$request = new WP_Http();
	$parms = array(
		'timeout' => 10,
		'method'  => $method,
	);
	$result = $request->request( $f, $parms );
	if ( empty( $result ) ) {
		return '';
	}
	if ( is_array( $result ) ) {
		$answer = $result['body'];
		return $answer;
	}
	if ( is_object( $result ) ) {
		$answer = 'ERR: ' . $result->get_error_message();
		return $answer;
	}
	return '';
}

function dam_spam_get_icon_urls() {
	return array(
		'up'	 => DAM_SPAM_URL . 'assets/images/up.png',
		'down'   => DAM_SPAM_URL . 'assets/images/down.png',
		'trash'  => DAM_SPAM_URL . 'assets/images/trash.png',
		'stop'   => DAM_SPAM_URL . 'assets/images/stop.png',
		'whois'  => DAM_SPAM_URL . 'assets/images/whois.png',
		'search' => DAM_SPAM_URL . 'assets/images/search.png',
	);
}

function dam_spam_get_ajax_allowed_html() {
	return array(
		'a' => array(
			'href' => array(),
			'onclick' => array(),
			'title' => array(),
			'alt' => array(),
			'target' => array(),
		),
		'img' => array(
			'src' => array(),
			'class' => array(),
			'alt' => array(),
		),
		'br' => array(),
		'tr' => array(),
		'td' => array(),
	);
}

function dam_spam_auto_migrate_from_stop_spammers() {
	if ( get_option( 'dam_spam_options' ) !== false ) {
		return;
	}
	$ss_options = get_option( 'ss_stop_sp_reg_options' );
	$ss_stats = get_option( 'ss_stop_sp_reg_stats' );
	if ( $ss_options === false ) {
		return;
	}
	$dam_spam_options = dam_spam_map_old_to_new_options( $ss_options );
	update_option( 'dam_spam_options', $dam_spam_options );
	if ( $ss_stats !== false ) {
		$dam_spam_stats = dam_spam_map_old_to_new_stats( $ss_stats );
		update_option( 'dam_spam_stats', $dam_spam_stats );
	}
	update_option( 'dam_spam_migrated_from_ss', gmdate( 'Y-m-d H:i:s' ) );
}

function dam_spam_auto_migrate_from_old_dam_spam() {
	if ( get_option( 'dam_spam_migrated_from_ds' ) !== false ) {
		return;
	}
	if ( get_option( 'dam_spam_options' ) !== false ) {
		return;
	}
	global $wpdb;
	$old_options = $wpdb->get_results(
		"SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'ds_%'",
		ARRAY_A
	);
	if ( empty( $old_options ) ) {
		return;
	}
	foreach ( $old_options as $option ) {
		$old_name = $option['option_name'];
		$new_name = str_replace( 'ds_', 'dam_spam_', $old_name );
		$value = maybe_unserialize( $option['option_value'] );
		if ( $old_name === 'ds_options' && is_array( $value ) ) {
			$new_value = array();
			foreach ( $value as $key => $val ) {
				if ( strpos( $key, 'ds_' ) === 0 ) {
					$new_key = str_replace( 'ds_', 'dam_spam_', $key );
					$new_value[$new_key] = $val;
				} else {
					$new_value[$key] = $val;
				}
			}
			$value = $new_value;
		}
		update_option( $new_name, $value );
	}
	update_option( 'dam_spam_migrated_from_ds', gmdate( 'Y-m-d H:i:s' ) );
}

function dam_spam_map_old_to_new_options( $old_options ) {
	$new_options = dam_spam_load( 'get_options', '' );
	$migration_map = array(
		'chkadminlog'		=> 'check_admin_log',
		'chkaws'			=> 'check_aws',
		'chkcloudflare'		=> 'check_cloudflare',
		'chkgcache'			=> 'check_good_cache',
		'chkgoogle'			=> 'check_google',
		'chkmiscallowlist'	=> 'check_misc_allow_list',
		'chkpaypal'			=> 'check_paypal',
		'chkform'			=> 'check_form',
		'chkscripts'		=> 'check_scripts',
		'chkvalidip'		=> 'check_valid_ip',
		'chkwlem'			=> 'check_allowed_email',
		'chkwluserid'		=> 'check_allowed_user_id',
		'chkwlist'			=> 'check_allow_list',
		'chkwlistemail'		=> 'check_allow_list_email',
		'chkstripe'			=> 'check_stripe',
		'chkauthorizenet'	=> 'check_authorize_net',
		'chkbraintree'		=> 'check_braintree',
		'chkrecurly'		=> 'check_recurly',
		'chksquare'			=> 'check_square',
		'ss_private_mode'	=> 'dam_spam_private_mode',
		'chk404'			=> 'check_404',
		'chkaccept'			=> 'check_accept',
		'chkadmin'			=> 'check_admin',
		'chkagent'			=> 'check_agent',
		'chkamazon'			=> 'check_amazon',
		'chkbcache'			=> 'check_bad_cache',
		'chkblem'			=> 'check_blocked_email',
		'chkbluserid'		=> 'check_blocked_user_id',
		'chkblip'			=> 'check_blocked_ip',
		'chkbotscout'		=> 'check_botscout',
		'chkdisp'			=> 'check_disposable',
		'chkdnsbl'			=> 'check_dnsbl',
		'chkexploits'		=> 'check_exploits',
		'chkgooglesafe'		=> 'check_google_safe',
		'chkhoney'			=> 'check_honeypot',
		'chkhosting'		=> 'check_hosting',
		'chkinvalidip'		=> 'check_invalid_ip',
		'chklong'			=> 'check_long',
		'chkshort'			=> 'check_short',
		'chkbbcode'			=> 'check_bbcode',
		'chkreferer'		=> 'check_referer',
		'chksession'		=> 'check_session',
		'chksfs'			=> 'check_sfs',
		'chkspamwords'		=> 'check_spam_words',
		'chkurlshort'		=> 'check_url_short',
		'chkurls'			=> 'check_urls',
		'chktld'			=> 'check_tld',
		'chkubiquity'		=> 'check_ubiquity',
		'chkakismet'		=> 'check_akismet',
		'chkmulti'			=> 'check_multi',
		'chktor'			=> 'check_tor',
		'chkperiods'		=> 'check_periods',
		'chkhyphens'		=> 'check_hyphens',
		'badagents'			=> 'bad_agents',
		'badTLDs'			=> 'bad_tlds',
		'blist'				=> 'block_list',
		'wlist'				=> 'allow_list',
		'wlist_email'		=> 'allow_list_email',
		'spamwords'			=> 'spam_words',
		'blockurlshortners'	=> 'block_url_shortners',
		'wlreqmail'			=> 'allow_list_request_email',
		'wlreq'				=> 'allow_list_request',
		'redirurl'			=> 'redirect_url',
		'logfilesize'		=> 'log_file_size',
		'rejectmessage'		=> 'reject_message',
		'multicnt'			=> 'multicount',
		'ss_sp_cache'		=> 'dam_spam_cache',
		'ss_sp_hist'		=> 'dam_spam_hist',
		'ss_sp_good'		=> 'dam_spam_good',
		'ss_sp_cache_em'	=> 'dam_spam_cache_em',
	);
	foreach ( $old_options as $old_key => $value ) {
		if ( isset( $migration_map[$old_key] ) ) {
			$new_options[$migration_map[$old_key]] = $value;
		} elseif ( strpos( $old_key, 'chk' ) !== 0 && strpos( $old_key, 'ss_' ) !== 0 && strpos( $old_key, 'cnt' ) !== 0 ) {
			$new_options[$old_key] = $value;
		}
	}
	$new_options['check_credit_card'] = 'Y';
	$new_options['check_woo_form'] = 'N';
	$new_options['check_gravity_form'] = 'N';
	$new_options['check_wp_form'] = 'N';
	$new_options['version'] = DAM_SPAM_VERSION;
	return $new_options;
}

function dam_spam_map_old_to_new_stats( $old_stats ) {
	$new_stats = dam_spam_load( 'get_stats', '' );
	$count_map = array(
		'cntchkaws'				=> 'count_check_aws',
		'cntchkcloudflare'		=> 'count_check_cloudflare',
		'cntchkgcache'			=> 'count_check_good_cache',
		'cntchkgoogle'			=> 'count_check_google',
		'cntchkmiscallowlist'	=> 'count_check_misc_allow_list',
		'cntchkpaypal'			=> 'count_check_paypal',
		'cntchkform'			=> 'count_check_form',
		'cntchkscripts'			=> 'count_check_scripts',
		'cntchkvalidip'			=> 'count_check_valid_ip',
		'cntchkwlem'			=> 'count_check_allowed_email',
		'cntchkwluserid'		=> 'count_check_allowed_user_id',
		'cntchkwlist'			=> 'count_check_allow_list',
		'cntcap'				=> 'count_captcha',
		'cntncap'				=> 'count_captcha_fail',
		'cntpass'				=> 'count_pass',
		'cntchk404'				=> 'count_check_404',
		'cntchkaccept'			=> 'count_check_accept',
		'cntchkadmin'			=> 'count_check_admin',
		'cntchkadminlog'		=> 'count_check_admin_log',
		'cntchkagent'			=> 'count_check_agent',
		'cntchkamazon'			=> 'count_check_amazon',
		'cntchkakismet'			=> 'count_check_akismet',
		'cntchkbcache'			=> 'count_check_bad_cache',
		'cntchkblem'			=> 'count_check_blocked_email',
		'cntchkuserid'			=> 'count_check_blocked_user_id',
		'cntchkblip'			=> 'count_check_blocked_ip',
		'cntchkbotscout'		=> 'count_check_botscout',
		'cntchkdisp'			=> 'count_check_disposable',
		'cntchkdnsbl'			=> 'count_check_dnsbl',
		'cntchkexploits'		=> 'count_check_exploits',
		'cntchkgooglesafe'		=> 'count_check_google_safe',
		'cntchkhoney'			=> 'count_check_honeypot',
		'cntchkhosting'			=> 'count_check_hosting',
		'cntchkinvalidip'		=> 'count_check_invalid_ip',
		'cntchklong'			=> 'count_check_long',
		'cntchkshort'			=> 'count_check_short',
		'cntchkbbcode'			=> 'count_check_bbcode',
		'cntchkreferer'			=> 'count_check_referer',
		'cntchksession'			=> 'count_check_session',
		'cntchksfs'				=> 'count_check_sfs',
		'cntchkspamwords'		=> 'count_check_spam_words',
		'cntchkchkurlshort'		=> 'count_check_url_short',
		'cntchktld'				=> 'count_check_tld',
		'cntchkubiquity'		=> 'count_check_ubiquity',
		'cntchkmulti'			=> 'count_check_multi',
		'wlrequests'			=> 'allow_list_requests',
		'spcount'				=> 'spam_count',
		'spmcount'				=> 'spam_multisite_count',
		'spmdate'				=> 'spam_multisite_date',
		'spdate'				=> 'spam_date',
	);
	foreach ( $old_stats as $old_key => $value ) {
		if ( isset( $count_map[$old_key] ) ) {
			$new_stats[$count_map[$old_key]] = $value;
		} elseif ( strpos( $old_key, 'cnt' ) !== 0 ) {
			$new_stats[$old_key] = $value;
		}
	}
	$new_stats['version'] = DAM_SPAM_VERSION;
	return $new_stats;
}

add_action( 'admin_init', 'dam_spam_migrate_allow_list_email' );
function dam_spam_migrate_allow_list_email() {
	$options = get_option( 'dam_spam_options' );
	if ( !$options || !is_array( $options ) ) {
		return;
	}
	if ( !isset( $options['allow_list_email'] ) || !is_array( $options['allow_list_email'] ) || empty( $options['allow_list_email'] ) ) {
		return;
	}
	$allow_list = isset( $options['allow_list'] ) && is_array( $options['allow_list'] ) ? $options['allow_list'] : array();
	foreach ( $options['allow_list_email'] as $email ) {
		if ( !in_array( $email, $allow_list, true ) ) {
			$allow_list[] = $email;
		}
	}
	$options['allow_list'] = $allow_list;
	$options['allow_list_email'] = array();
	update_option( 'dam_spam_options', $options );
}

function dam_spam_write_ban_file() {
	$manual_bans_raw = get_option( 'dam_spam_manual_bans', '' );
	$automatic_bans = get_option( 'dam_spam_automatic_bans', array() );
	$manual_bans = array();
	if ( !empty( $manual_bans_raw ) ) {
		$lines = explode( "\n", $manual_bans_raw );
		foreach ( $lines as $line ) {
			$ip = trim( $line );
			if ( !empty( $ip ) && filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				$manual_bans[$ip] = true;
			}
		}
	}
	if ( !is_array( $automatic_bans ) ) {
		$automatic_bans = array();
	}
	$all_bans = array_merge( $manual_bans, $automatic_bans );
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}
	if ( !$wp_filesystem ) {
		return;
	}
	$mu_plugin_file = WP_CONTENT_DIR . '/mu-plugins/dam-spam-banner.php';
	if ( empty( $all_bans ) ) {
		if ( file_exists( $mu_plugin_file ) ) {
			wp_delete_file( $mu_plugin_file );
		}
		return;
	}
	$mu_plugin_content = "<?php\n";
	$mu_plugin_content .= "/*\n";
	$mu_plugin_content .= "Plugin Name: Dam Spam Banner\n";
	$mu_plugin_content .= "Description: Loads Dam Spam IP ban list early to block banned IPs before WordPress fully loads.\n";
	$mu_plugin_content .= "*/\n\n";
	$mu_plugin_content .= "\$visitor_ip = '';\n\n";
	$mu_plugin_content .= "if ( isset( \$_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {\n";
	$mu_plugin_content .= "\t\$visitor_ip = \$_SERVER['HTTP_CF_CONNECTING_IP'];\n";
	$mu_plugin_content .= "} elseif ( isset( \$_SERVER['HTTP_X_REAL_IP'] ) ) {\n";
	$mu_plugin_content .= "\t\$visitor_ip = \$_SERVER['HTTP_X_REAL_IP'];\n";
	$mu_plugin_content .= "} elseif ( isset( \$_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {\n";
	$mu_plugin_content .= "\t\$ips = explode( ',', \$_SERVER['HTTP_X_FORWARDED_FOR'] );\n";
	$mu_plugin_content .= "\t\$visitor_ip = trim( \$ips[0] );\n";
	$mu_plugin_content .= "} elseif ( isset( \$_SERVER['REMOTE_ADDR'] ) ) {\n";
	$mu_plugin_content .= "\t\$visitor_ip = \$_SERVER['REMOTE_ADDR'];\n";
	$mu_plugin_content .= "}\n\n";
	$mu_plugin_content .= "\$dam_spam_banned_ips = array(\n";
	foreach ( array_keys( $all_bans ) as $ip ) {
		$mu_plugin_content .= "\t'" . esc_sql( $ip ) . "' => true,\n";
	}
	$mu_plugin_content .= ");\n\n";
	$mu_plugin_content .= "if ( !empty( \$visitor_ip ) && filter_var( \$visitor_ip, FILTER_VALIDATE_IP ) && isset( \$dam_spam_banned_ips[\$visitor_ip] ) ) {\n";
	$mu_plugin_content .= "\theader( 'Connection: close' );\n";
	$mu_plugin_content .= "\tignore_user_abort( true );\n";
	$mu_plugin_content .= "\tob_start();\n";
	$mu_plugin_content .= "\theader( 'Content-Length: 0' );\n";
	$mu_plugin_content .= "\tob_end_flush();\n";
	$mu_plugin_content .= "\tflush();\n";
	$mu_plugin_content .= "\texit;\n";
	$mu_plugin_content .= "}";
	$mu_plugins_dir = WP_CONTENT_DIR . '/mu-plugins';
	if ( !$wp_filesystem->is_dir( $mu_plugins_dir ) ) {
		$wp_filesystem->mkdir( $mu_plugins_dir, FS_CHMOD_DIR );
	}
	$wp_filesystem->put_contents( $mu_plugin_file, $mu_plugin_content, FS_CHMOD_FILE );
}

?>