<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_invalid_ip {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( strpos( $ip, '.' ) === false && strpos( $ip, ':' ) === false ) {
			return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
		}
		if ( defined( 'AF_INET6' ) && strpos( $ip, ':' ) !== false ) {
			try {
				if ( !@inet_pton( $ip ) ) {
					return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
				}
			} catch ( Exception $e ) {
				return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
			}
		}
		$ips = ds_module::ip2numstr( $ip );
		if ( $ips >= '224000000000' && $ips <= '239255255255' ) {
			return esc_html__( 'IPv4 Multicast Address Space Registry', 'dam-spam' );
		}
		if ( $ips >= '240000000000' && $ips <= '255255255255' ) {
			return esc_html__( 'Reserved for future use', 'dam-spam' );
		}
		return false;
	}
}

?>