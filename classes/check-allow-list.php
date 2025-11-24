<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_allow_list extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$email = $post['email'];
		$ip = dam_spam_get_ip();
		$addons = array();
		$addons = apply_filters( 'dam_spam_addons_allow', $addons );
		if ( !empty( $addons ) && is_array( $addons ) ) {
			foreach ( $addons as $add ) {
				if ( !empty( $add ) && is_array( $add ) ) {
					$reason = dam_spam_load( $add, dam_spam_get_ip(), $stats, $options, $post );
					if ( $reason !== false ) {
						dam_spam_log_good( dam_spam_get_ip(), $reason, $add[1], $add );
						return $reason;
					}
				}
			}
		}
		$actions = array(
			'check_cloudflare',
			'check_admin_log',
			'check_aws',
			'check_good_cache',
			'check_general_allow_list',
			'check_google',
			'check_misc_allow_list',
			'check_paypal',
			'check_form',
			'check_woo_form',
			'check_gravity_form',
			'check_wp_form',
			'check_scripts',
			'check_allowed_email',
			'check_allowed_user_id',
			'check_allow_list_ip',
			'check_allow_list_email',
			'check_yahoo_merchant'
		);
		if ( !isset( $options['check_allow_list_email'] ) ) {
			$options['check_allow_list_email'] = 'Y';
		}
		foreach ( $actions as $check ) {
			if ( $options[$check] == 'Y' ) {
				$reason = dam_spam_load( $check, dam_spam_get_ip(), $stats, $options, $post );
				if ( $reason !== false ) {
					dam_spam_log_good( dam_spam_get_ip(), $reason, $check );
					return $reason;
				}
			} else {
			}
		}
		return false;
	}
}

?>