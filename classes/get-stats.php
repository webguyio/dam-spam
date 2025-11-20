<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class get_stats {
	public function process(
		$ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$stats = get_option( 'ds_stats' );
		if ( empty( $stats ) || !is_array( $stats ) ) {
			$stats = array();
		}
		$defaults = array(
			'badips'	 => array(),
			'goodips'	 => array(),
			'hist'	     => array(),
			'allow_list_requests' => array(),
			'addonstats' => array(),
			'multi'	     => array()
		);
		$defaultsWL = array(
			'count_check_aws'				 => 0,
			'count_check_cloudflare'		 => 0,
			'count_check_good_cache'		 => 0,
			'count_check_general_allow_list' => 0,
			'count_check_google'			 => 0,
			'count_check_misc_allow_list'	 => 0,
			'count_check_paypal'			 => 0,
			'count_check_form'				 => 0,
			'count_check_scripts'			 => 0,
			'count_check_valid_ip'			 => 0,
			'count_check_allowed_email'		 => 0,
			'count_check_allowed_user_id'	 => 0,
			'count_check_allow_list'		 => 0,
			'count_check_yahoo_merchant'	 => 0
		);
		$defaultsBL = array(
			'count_check_404'			  => 0,
			'count_check_accept'		  => 0,
			'count_check_admin'			  => 0,
			'count_check_admin_log'		  => 0,
			'count_check_agent'			  => 0,
			'count_check_amazon'		  => 0,
			'count_check_akismet'		  => 0,
			'count_check_bad_cache'		  => 0,
			'count_check_blocked_email'	  => 0,
			'count_check_blocked_user_id' => 0,
			'count_check_blocked_ip'	  => 0,
			'count_check_botscout'		  => 0,
			'count_check_disposable'	  => 0,
			'count_check_dnsbl'			  => 0,
			'count_check_exploits'		  => 0,
			'count_check_google_safe'	  => 0,
			'count_check_honeypot'		  => 0,
			'count_check_hosting'		  => 0,
			'count_check_invalid_ip'	  => 0,
			'count_check_long'			  => 0,
			'count_check_short'			  => 0,
			'count_check_bbcode'		  => 0,
			'count_check_referer'		  => 0,
			'count_check_session'		  => 0,
			'count_check_sfs'			  => 0,
			'count_check_spam_words'	  => 0,
			'count_check_url_short'		  => 0,
			'count_check_tld'			  => 0,
			'count_check_ubiquity'		  => 0,
			'count_check_multi'			  => 0
		);
		$defaultsTOTALS = array(
			'spam_count'		   => 0,
			'spam_multisite_count' => 0,
			'count_captcha'		   => 0,
			'count_captcha_fail'   => 0,
			'count_pass'		   => 0,
			'spam_multisite_date'  => gmdate( 'Y/m/d', time() + ( get_option( 'gmt_offset' ) * 3600 ) ),
			'spam_date'			   => gmdate( 'Y/m/d', time() + ( get_option( 'gmt_offset' ) * 3600 ) )
		);
		$answer = array_merge( $defaults, $defaultsWL, $defaultsTOTALS, $defaultsBL );
		foreach ( $answer as $key => $val ) {
			if ( array_key_exists( $key, $stats ) ) {
				$answer[$key] = $stats[$key];
			}
		}
		if ( !is_array( $answer['allow_list_requests'] ) ) {
			$answer['allow_list_requests'] = array();
		}
		if ( !is_array( $answer['badips'] ) ) {
			$answer['badips'] = array();
		}
		if ( !is_array( $answer['hist'] ) ) {
			$answer['hist'] = array();
		}
		if ( !is_array( $answer['addonstats'] ) ) {
			$answer['addonstats'] = array();
		}
		if ( !is_array( $answer['goodips'] ) ) {
			$answer['goodips'] = array();
		}
		if ( !is_numeric( $answer['spam_count'] ) ) {
			$answer['spam_count'] = 0;
		}
		if ( !is_numeric( $answer['spam_multisite_count'] ) ) {
			$answer['spam_multisite_count'] = 0;
		}
		if ( $answer['spam_count'] == 0 ) {
			$answer['spam_date'] = gmdate( 'Y/m/d', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		}
		if ( $answer['spam_multisite_count'] == 0 ) {
			$answer['spam_multisite_date'] = gmdate( 'Y/m/d', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		}
		$answer['version'] = DS_VERSION;
		ds_set_stats( $answer );
		return $answer;
	}
}

?>