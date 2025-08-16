<?php
// checks for invalid IPs

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkinvalidip {
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
		$ips = be_module::ip2numstr( $ip );
		if ( $ips >= '224000000000' && $ips <= '239255255255' ) {
			return esc_html__( 'IPv4 Multicast Address Space Registry', 'dam-spam' );
		}
		// reserved for future use >= 240.0.0.0
		if ( $ips >= '240000000000' && $ips <= '255255255255' ) {
			return esc_html__( 'Reserved for future use', 'dam-spam' );
		}
		return false;
	}
}

?>