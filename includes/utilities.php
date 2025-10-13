<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

function ds_read_file( $f, $method = 'GET' ) {
	if ( !class_exists( 'WP_Http' ) ) {
		include_once ABSPATH . WPINC . '/class-http.php';
	}
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

function ds_auto_migrate_from_stop_spammers() {
	if ( get_option( 'ds_options' ) !== false ) {
		return;
	}
	$ss_options = get_option( 'ss_stop_sp_reg_options' );
	$ss_stats = get_option( 'ss_stop_sp_reg_stats' );
	if ( $ss_options === false ) {
		return;
	}
	$ds_options = ds_map_old_to_new_options( $ss_options );
	update_option( 'ds_options', $ds_options );
	if ( $ss_stats !== false ) {
		$ds_stats = ds_map_old_to_new_stats( $ss_stats );
		update_option( 'ds_stats', $ds_stats );
	}
	update_option( 'ds_migrated_from_ss', gmdate( 'Y-m-d H:i:s' ) );
}

function ds_map_old_to_new_options( $old_options ) {
	$new_options = ds_load( 'get_options', '' );
	$migration_map = array(
		'chkadminlog'		=> 'check_admin_log',
		'chkaws'			=> 'check_aws',
		'chkcloudflare'		=> 'check_cloudflare',
		'chkgcache'			=> 'check_good_cache',
		'chkgenallowlist'	=> 'check_general_allow_list',
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
		'chkyahoomerchant'	=> 'check_yahoo_merchant',
		'chkstripe'			=> 'check_stripe',
		'chkauthorizenet'	=> 'check_authorize_net',
		'chkbraintree'		=> 'check_braintree',
		'chkrecurly'		=> 'check_recurly',
		'chksquare'			=> 'check_square',
		'ss_private_mode'	=> 'ds_private_mode',
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
		'payoptions'		=> 'pay_options',
		'wlreqmail'			=> 'allow_list_request_email',
		'wlreq'				=> 'allow_list_request',
		'redirurl'			=> 'redirect_url',
		'logfilesize'		=> 'log_file_size',
		'rejectmessage'		=> 'reject_message',
		'multicnt'			=> 'multicount',
		'ss_sp_cache'		=> 'ds_cache',
		'ss_sp_hist'		=> 'ds_hist',
		'ss_sp_good'		=> 'ds_good',
		'ss_sp_cache_em'	=> 'ds_cache_em',
	);
	$countries = array( 'ad', 'ae', 'af', 'al', 'am', 'ar', 'at', 'au', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bg', 'bh', 'bn', 'bo', 'br', 'bs', 'by', 'bz', 'ca', 'cd', 'ch', 'cl', 'cn', 'co', 'cr', 'cu', 'cw', 'cy', 'cz', 'de', 'dk', 'do', 'dz', 'ec', 'ee', 'es', 'eu', 'fi', 'fj', 'fr', 'gb', 'ge', 'gf', 'gi', 'gp', 'gr', 'gt', 'gu', 'gy', 'hk', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'in', 'iq', 'ir', 'is', 'it', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lk', 'lt', 'lu', 'lv', 'md', 'me', 'mk', 'mm', 'mn', 'mo', 'mp', 'mq', 'mt', 'mv', 'mx', 'my', 'nc', 'ni', 'nl', 'no', 'np', 'nz', 'om', 'pa', 'pe', 'pg', 'ph', 'pk', 'pl', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 'ro', 'rs', 'ru', 'sa', 'sc', 'se', 'sg', 'si', 'sk', 'sv', 'sx', 'sy', 'th', 'tj', 'tm', 'tr', 'tt', 'tw', 'ua', 'uk', 'us', 'uy', 'uz', 'vc', 've', 'vn', 'ye' );
	foreach ( $countries as $cc ) {
		$migration_map['chk' . strtoupper( $cc )] = 'check_' . $cc;
	}
	foreach ( $old_options as $old_key => $value ) {
		if ( isset( $migration_map[$old_key] ) ) {
			$new_options[$migration_map[$old_key]] = $value;
		} elseif ( strpos( $old_key, 'chk' ) !== 0 && strpos( $old_key, 'ss_' ) !== 0 && strpos( $old_key, 'cnt' ) !== 0 ) {
			$new_options[$old_key] = $value;
		}
	}
	$new_options['check_woo_form'] = 'N';
	$new_options['check_gravity_form'] = 'N';
	$new_options['check_wp_form'] = 'N';
	$new_options['version'] = DS_VERSION;
	return $new_options;
}

function ds_map_old_to_new_stats( $old_stats ) {
	$new_stats = ds_load( 'get_stats', '' );
	$count_map = array(
		'cntchkaws'				=> 'count_check_aws',
		'cntchkcloudflare'		=> 'count_check_cloudflare',
		'cntchkgcache'			=> 'count_check_good_cache',
		'cntchkgenallowlist'	=> 'count_check_general_allow_list',
		'cntchkgoogle'			=> 'count_check_google',
		'cntchkmiscallowlist'	=> 'count_check_misc_allow_list',
		'cntchkpaypal'			=> 'count_check_paypal',
		'cntchkform'			=> 'count_check_form',
		'cntchkscripts'			=> 'count_check_scripts',
		'cntchkvalidip'			=> 'count_check_valid_ip',
		'cntchkwlem'			=> 'count_check_allowed_email',
		'cntchkwluserid'		=> 'count_check_allowed_user_id',
		'cntchkwlist'			=> 'count_check_allow_list',
		'cntchkyahoomerchant'	=> 'count_check_yahoo_merchant',
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
	$countries = array( 'ad', 'ae', 'af', 'al', 'am', 'ar', 'at', 'au', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bg', 'bh', 'bn', 'bo', 'br', 'bs', 'by', 'bz', 'ca', 'cd', 'ch', 'cl', 'cn', 'co', 'cr', 'cu', 'cw', 'cy', 'cz', 'de', 'dk', 'do', 'dz', 'ec', 'ee', 'es', 'eu', 'fi', 'fj', 'fr', 'gb', 'ge', 'gf', 'gi', 'gp', 'gr', 'gt', 'gu', 'gy', 'hk', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'in', 'iq', 'ir', 'is', 'it', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lk', 'lt', 'lu', 'lv', 'md', 'me', 'mk', 'mm', 'mn', 'mo', 'mp', 'mq', 'mt', 'mv', 'mx', 'my', 'nc', 'ni', 'nl', 'no', 'np', 'nz', 'om', 'pa', 'pe', 'pg', 'ph', 'pk', 'pl', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 'ro', 'rs', 'ru', 'sa', 'sc', 'se', 'sg', 'si', 'sk', 'sv', 'sx', 'sy', 'th', 'tj', 'tm', 'tr', 'tt', 'tw', 'ua', 'uk', 'us', 'uy', 'uz', 'vc', 've', 'vn', 'ye' );
	foreach ( $countries as $cc ) {
		$count_map['cntchk' . strtoupper( $cc )] = 'count_check_' . $cc;
	}
	foreach ( $old_stats as $old_key => $value ) {
		if ( isset( $count_map[$old_key] ) ) {
			$new_stats[$count_map[$old_key]] = $value;
		} elseif ( strpos( $old_key, 'cnt' ) !== 0 ) {
			$new_stats[$old_key] = $value;
		}
	}
	$new_stats['version'] = DS_VERSION;
	return $new_stats;
}

?>