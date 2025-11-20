<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_post extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$reason = ds_load( 'check_good_cache', ds_get_ip(), $stats, $options, $post );
		if ( $reason !== false ) {
			return;
		}
		$addons = array();
		$addons = apply_filters( 'ds_addons_block', $addons );
		if ( !empty( $addons ) && is_array( $addons ) ) {
			foreach ( $addons as $add ) {
				if ( !empty( $add ) && is_array( $add ) ) {
					$reason = ds_load( $add, ds_get_ip(), $stats, $options,
						$post );
					if ( $reason !== false ) {
						ds_log_bad( ds_get_ip(), $reason, $add[1], $add );
						exit();
					}
				}
			}
		}
		$noipactions = array(
			'check_agent',
			'check_bbcode',
			'check_blocked_email',
			'check_blocked_user_id',
			'check_disposable',
			'check_exploits',
			'check_long',
			'check_short',
			'check_referer',
			'check_session',
			'check_spam_words',
			'check_periods',
			'check_url_short',
			'check_tld',
			'check_accept',
			'check_admin',
			'check_urls'
		);
		$actions = array(
			'check_amazon',
			'check_bad_cache',
			'check_blocked_ip',
			'check_disposable',
			'check_hosting',
			'check_invalid_ip',
			'check_ubiquity',
			'check_multi',
			'check_google_safe',
			'check_sfs',
			'check_honeypot',
			'check_botscout',
			'check_dnsbl'
		);
		$check = '';
		foreach ( $noipactions as $check ) {
			if ( $options[$check] == 'Y' ) {
				$reason = ds_load( $check, ds_get_ip(), $stats, $options, $post );
				if ( $reason !== false ) {
					break;
				}
			}
		}
		if ( $reason === false ) {
			$actionvalid = array( 'check_valid_ip' );
			foreach ( $actionvalid as $check ) {
				$reason = ds_load( $check, ds_get_ip(), $stats, $options, $post );
				if ( $reason !== false ) {
					break;
				}
			}
			if ( $reason !== false ) {
				return false;
			}
		}
		if ( $reason === false ) {
			foreach ( $actions as $check ) {
				if ( $options[$check] == 'Y' ) {
					$reason = ds_load( $check, ds_get_ip(), $stats, $options, $post );
					if ( $reason !== false ) {
						break;
					}
				}
			}
		}
		if ( array_key_exists( 'email', $post ) && $post['email'] == 'email@example.com' ) {
			$post['reason'] = esc_html__( 'Testing Email (will always be blocked)', 'dam-spam' );
			ds_load( 'challenge', ds_get_ip(), $stats, $options, $post );
			return;
		}
		if ( $reason === false ) {
			return false;
		}
		ds_log_bad( ds_get_ip(), $reason, $check );
		exit;
	}
}

?>