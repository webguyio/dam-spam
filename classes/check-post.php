<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_post extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$reason = dam_spam_load( 'check_good_cache', dam_spam_get_ip(), $stats, $options, $post );
		if ( $reason !== false ) {
			return;
		}
		$addons = array();
		$addons = apply_filters( 'dam_spam_addons_block', $addons );
		if ( !empty( $addons ) && is_array( $addons ) ) {
			foreach ( $addons as $add ) {
				if ( !empty( $add ) && is_array( $add ) ) {
					$reason = dam_spam_load( $add, dam_spam_get_ip(), $stats, $options,
						$post );
					if ( $reason !== false ) {
						dam_spam_log_bad( dam_spam_get_ip(), $reason, $add[1], $add );
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
				$reason = dam_spam_load( $check, dam_spam_get_ip(), $stats, $options, $post );
				if ( $reason !== false ) {
					break;
				}
			}
		}
		if ( $reason === false ) {
			$actionvalid = array( 'check_valid_ip' );
			foreach ( $actionvalid as $check ) {
				$reason = dam_spam_load( $check, dam_spam_get_ip(), $stats, $options, $post );
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
					$reason = dam_spam_load( $check, dam_spam_get_ip(), $stats, $options, $post );
					if ( $reason !== false ) {
						break;
					}
				}
			}
		}
		if ( array_key_exists( 'email', $post ) && $post['email'] == 'email@example.com' ) {
			$post['reason'] = esc_html__( 'Testing Email (will always be blocked)', 'dam-spam' );
			dam_spam_load( 'challenge', dam_spam_get_ip(), $stats, $options, $post );
			return;
		}
		if ( $reason === false ) {
			return false;
		}
		dam_spam_log_bad( dam_spam_get_ip(), $reason, $check );
		exit;
	}
}

?>