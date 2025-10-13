<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// last updated from https://www.cloudflare.com/ips/ on 2/29/24
class check_cloudflare extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( function_exists( 'cloudflare_init' ) ) {
			return false;
		}
		if ( !array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) ) {
			return false;
		}
		$ip4ranges = array(
			'103.21.244.0/22',
			'103.22.200.0/22',
			'103.31.4.0/22',
			'104.16.0.0/13',
			'104.24.0.0/14',
			'108.162.192.0/18',
			'131.0.72.0/22',
			'141.101.64.0/18',
			'162.158.0.0/15',
			'172.64.0.0/13',
			'173.245.48.0/20',
			'188.114.96.0/20',
			'190.93.240.0/20',
			'197.234.240.0/22',
			'198.41.128.0/17'
		);
		$ip6ranges = array(
			'2400:cb00::/32',
			'2405:8100::/32',
			'2405:b500::/32',
			'2606:4700::/32',
			'2803:f800::/32',
			'2a06:98c0::/29',
			'2c0f:f248::/32'
		);
		$cf_found  = false;
		if ( strpos( $ip, '.' ) !== false ) {
			$ipl = ip2long( $ip );
			foreach ( $ip4ranges as $ip4 ) {
				list( $range, $bits ) = explode( '/', $ip4, 2 );
				$ipr = ip2long( $range );
				$mask = - 1 << ( 32 - $bits );
				$ipt = $ipl & $mask;
				$ipr = $ipr & $mask;
				if ( $ipt == $ipr ) {
					$cf_found = true;
					break;
				}
			}
		} else if ( strpos( $ip, ':' ) !== false && strlen( $ip ) >= 9 ) {
			$ip = strtolower( $ip );
			foreach ( $ip6ranges as $ip6 ) {
				if ( substr( $ip6, 0, 9 ) == substr( $ip, 0, 9 ) ) {
					$cf_found = true;
					break;
				}
			}
		}
		if ( !$cf_found ) {
			return false;
		}
		if ( array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) ) {
			if ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) ) {
				$_SERVER['REMOTE_ADDR'] = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) : '';
				return false;
			}
		}
		return false;
	}
}

?>