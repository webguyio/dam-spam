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
		$defaultsCountries = array(
			'count_check_ad' => 0,
			'count_check_ae' => 0,
			'count_check_af' => 0,
			'count_check_al' => 0,
			'count_check_am' => 0,
			'count_check_ar' => 0,
			'count_check_at' => 0,
			'count_check_au' => 0,
			'count_check_ax' => 0,
			'count_check_az' => 0,
			'count_check_ba' => 0,
			'count_check_bb' => 0,
			'count_check_bd' => 0,
			'count_check_be' => 0,
			'count_check_bg' => 0,
			'count_check_bh' => 0,
			'count_check_bn' => 0,
			'count_check_bo' => 0,
			'count_check_br' => 0,
			'count_check_bs' => 0,
			'count_check_by' => 0,
			'count_check_bz' => 0,
			'count_check_ca' => 0,
			'count_check_cd' => 0,
			'count_check_ch' => 0,
			'count_check_cl' => 0,
			'count_check_cn' => 0,
			'count_check_co' => 0,
			'count_check_cr' => 0,
			'count_check_cu' => 0,
			'count_check_cw' => 0,
			'count_check_cy' => 0,
			'count_check_cz' => 0,
			'count_check_de' => 0,
			'count_check_dk' => 0,
			'count_check_do' => 0,
			'count_check_dz' => 0,
			'count_check_ec' => 0,
			'count_check_ee' => 0,
			'count_check_es' => 0,
			'count_check_eu' => 0,
			'count_check_fi' => 0,
			'count_check_fj' => 0,
			'count_check_fr' => 0,
			'count_check_gb' => 0,
			'count_check_ge' => 0,
			'count_check_gf' => 0,
			'count_check_gi' => 0,
			'count_check_gp' => 0,
			'count_check_gr' => 0,
			'count_check_gt' => 0,
			'count_check_gu' => 0,
			'count_check_gy' => 0,
			'count_check_hk' => 0,
			'count_check_hn' => 0,
			'count_check_hr' => 0,
			'count_check_ht' => 0,
			'count_check_hu' => 0,
			'count_check_id' => 0,
			'count_check_ie' => 0,
			'count_check_il' => 0,
			'count_check_in' => 0,
			'count_check_iq' => 0,
			'count_check_ir' => 0,
			'count_check_is' => 0,
			'count_check_it' => 0,
			'count_check_jm' => 0,
			'count_check_jo' => 0,
			'count_check_jp' => 0,
			'count_check_ke' => 0,
			'count_check_kg' => 0,
			'count_check_kh' => 0,
			'count_check_kr' => 0,
			'count_check_kw' => 0,
			'count_check_ky' => 0,
			'count_check_kz' => 0,
			'count_check_la' => 0,
			'count_check_lb' => 0,
			'count_check_lk' => 0,
			'count_check_lt' => 0,
			'count_check_lu' => 0,
			'count_check_lv' => 0,
			'count_check_md' => 0,
			'count_check_me' => 0,
			'count_check_mk' => 0,
			'count_check_mm' => 0,
			'count_check_mn' => 0,
			'count_check_mo' => 0,
			'count_check_mp' => 0,
			'count_check_mq' => 0,
			'count_check_mt' => 0,
			'count_check_mv' => 0,
			'count_check_mx' => 0,
			'count_check_my' => 0,
			'count_check_nc' => 0,
			'count_check_ni' => 0,
			'count_check_nl' => 0,
			'count_check_no' => 0,
			'count_check_np' => 0,
			'count_check_nz' => 0,
			'count_check_om' => 0,
			'count_check_pa' => 0,
			'count_check_pe' => 0,
			'count_check_pg' => 0,
			'count_check_ph' => 0,
			'count_check_pk' => 0,
			'count_check_pl' => 0,
			'count_check_pr' => 0,
			'count_check_ps' => 0,
			'count_check_pt' => 0,
			'count_check_pw' => 0,
			'count_check_py' => 0,
			'count_check_qa' => 0,
			'count_check_ro' => 0,
			'count_check_rs' => 0,
			'count_check_ru' => 0,
			'count_check_sa' => 0,
			'count_check_sc' => 0,
			'count_check_se' => 0,
			'count_check_sg' => 0,
			'count_check_si' => 0,
			'count_check_sk' => 0,
			'count_check_sv' => 0,
			'count_check_sx' => 0,
			'count_check_sy' => 0,
			'count_check_th' => 0,
			'count_check_tj' => 0,
			'count_check_tm' => 0,
			'count_check_tr' => 0,
			'count_check_tt' => 0,
			'count_check_tw' => 0,
			'count_check_ua' => 0,
			'count_check_uk' => 0,
			'count_check_us' => 0,
			'count_check_uy' => 0,
			'count_check_uz' => 0,
			'count_check_vc' => 0,
			'count_check_ve' => 0,
			'count_check_vn' => 0,
			'count_check_ye' => 0
		);
		$answer = array_merge( $defaults, $defaultsWL, $defaultsTOTALS, $defaultsBL, $defaultsCountries );
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