<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class add_to_allow_list {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		$allow_list = $options['allow_list'];
		if ( !isset( $options['allow_list_email'] ) ) {
			$allow_list_email = array();
		} else {
			$allow_list_email = $options['allow_list_email'];
		}
		if ( !in_array( $ip, $allow_list ) ) {
			$allow_list[] = $ip;
		}
		$options['allow_list'] = $allow_list;
		if ( isset( $_GET['email'] ) && is_email( wp_unslash( $_GET['email'] ) ) ) {
			$email = sanitize_email( wp_unslash( $_GET['email'] ) );
			if ( !in_array( $email, $allow_list_email ) ) {
				$allow_list_email[] = $email;
			}
		}
		$options['allow_list_email'] = $allow_list_email;
		ds_set_options( $options );
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
		ds_set_stats( $stats );
		if ( isset( $_GET['func'] ) && sanitize_key( $_GET['func'] ) === 'add_white' ) {
			$this->ds_send_approval_email( $ip, $stats, $options, $post );
		}
		return false;
	}

	public function ds_send_approval_email( $ip, $stats = array(), $options = array(), $post = array() ) {
		if ( !array_key_exists( 'email_request', $options ) ) {
			return false;
		}
		if ( $options['email_request'] === 'N' ) {
			return false;
		}
		if ( !isset( $_GET['ip'] ) ) {
			return false;
		}
		$get_ip = sanitize_text_field( wp_unslash( $_GET['ip'] ) );
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
		$headers = esc_html__( 'From: ', 'dam-spam' ) . get_option( 'admin_email' ) . "\r\n";
		wp_mail( $to, $subject, $message, $headers );
		return true;
	}
}

?>
