<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified when called via AJAX; email parameter only processed with valid nonce
class dam_spam_add_to_block_list {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		$block_list = is_array( $options['block_list'] ) ? $options['block_list'] : array();
		$sanitized_ip = filter_var( $ip, FILTER_VALIDATE_IP );
		if ( $sanitized_ip && !in_array( $sanitized_ip, $block_list, true ) ) {
			$block_list[] = $sanitized_ip;
		}
		if ( isset( $_POST['email'] ) && is_email( wp_unslash( $_POST['email'] ) ) ) {
			if ( !isset( $_POST['func_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['func_nonce'] ) ), 'dam_spam_process_add_black' ) ) {
				return false;
			}
			$sanitized_email = sanitize_email( wp_unslash( $_POST['email'] ) );
			if ( !in_array( $sanitized_email, $block_list, true ) ) {
				$block_list[] = $sanitized_email;
			}
		}
		$options['block_list'] = $block_list;
		dam_spam_set_options( $options );
		$badips = $stats['badips'];
		if ( array_key_exists( $ip, $badips ) ) {
			unset( $badips[$ip] );
			$stats['badips'] = $badips;
		}
		$goodips = $stats['goodips'];
		if ( array_key_exists( $ip, $goodips ) ) {
			unset( $goodips[$ip] );
			$stats['goodips'] = $goodips;
		}
		dam_spam_set_stats( $stats );
		return false;
	}
}

?>