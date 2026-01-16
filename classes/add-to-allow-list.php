<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified when called via AJAX; email parameter only processed with valid nonce
class dam_spam_add_to_allow_list {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		$allow_list = is_array( $options['allow_list'] ) ? $options['allow_list'] : array();
		$sanitized_ip = filter_var( $ip, FILTER_VALIDATE_IP );
		if ( $sanitized_ip && !in_array( $sanitized_ip, $allow_list, true ) ) {
			$allow_list[] = $sanitized_ip;
		}
		if ( isset( $_POST['email'] ) && is_email( wp_unslash( $_POST['email'] ) ) ) {
			if ( !isset( $_POST['func_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['func_nonce'] ) ), 'dam_spam_process_add_white' ) ) {
				return false;
			}
			$sanitized_email = sanitize_email( wp_unslash( $_POST['email'] ) );
			if ( !in_array( $sanitized_email, $allow_list, true ) ) {
				$allow_list[] = $sanitized_email;
			}
		}
		$options['allow_list'] = $allow_list;
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
		if ( isset( $_POST['func'] ) && sanitize_key( $_POST['func'] ) === 'add_white' ) {
			$this->dam_spam_send_approval_email( $ip, $stats, $options, $post );
		}
		return false;
	}

	public function dam_spam_send_approval_email( $ip, $stats = array(), $options = array(), $post = array() ) {
		if ( !array_key_exists( 'email_request', $options ) ) {
			return false;
		}
		if ( $options['email_request'] === 'N' ) {
			return false;
		}
		if ( !isset( $_POST['ip'] ) ) {
			return false;
		}
		$get_ip = sanitize_text_field( wp_unslash( $_POST['ip'] ) );
		$allow_list_requests = $stats['allow_list_requests'];
		$request = array();
		foreach ( $allow_list_requests as $r ) {
			if ( $r[0] === $get_ip ) {
				$request = $r;
				break;
			}
		}
		if ( empty( $request ) || !isset( $request[1] ) ) {
			return false;
		}
		$to = $request[1];
		if ( !is_email( $to ) ) {
			return false;
		}
		$ke = sanitize_email( $to );
		$blog = get_bloginfo( 'name' );
		// translators: %s is the website name
		$subject = sprintf( esc_html__( '%s: Your Request Has Been Approved', 'dam-spam' ), $blog );
		$subject = str_replace( '&', 'and', $subject );
		// translators: %s is the website name
		$message = sprintf( esc_html__( 'Apologies for the inconvenience. You have now been cleared for %s.', 'dam-spam' ), $blog );
		$message = str_replace( '&', 'and', $message );
		$headers = 'From: ' . sanitize_email( get_option( 'admin_email' ) ) . "\r\n";
		wp_mail( $to, $subject, $message, $headers );
		return true;
	}
}

?>
