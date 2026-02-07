<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// last updated from https://www.cloudflare.com/ips/ on 2/29/24
class dam_spam_check_cloudflare extends dam_spam_module {
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
			'198.41.128.0/17',
		);
		$ip6ranges = array(
			'2400:cb00::/32',
			'2405:8100::/32',
			'2405:b500::/32',
			'2606:4700::/32',
			'2803:f800::/32',
			'2a06:98c0::/29',
			'2c0f:f248::/32',
		);
		$bin_ip = inet_pton( $ip );
		if ( $bin_ip === false ) {
			return false;
		}
		$cf_ranges = strlen( $bin_ip ) === 4 ? $ip4ranges : $ip6ranges;
		$len = strlen( $bin_ip );
		foreach ( $cf_ranges as $range ) {
			list( $net, $bits ) = explode( '/', $range, 2 );
			$bin_net = inet_pton( $net );
			if ( $bin_net === false || strlen( $bin_net ) !== $len ) {
				continue;
			}
			$mask = str_repeat( "\xff", (int) ( $bits / 8 ) ) . ( $bits % 8 ? chr( 0xff << ( 8 - $bits % 8 ) ) : '' );
			$mask = str_pad( $mask, $len, "\x00" );
			if ( ( $bin_ip & $mask ) === ( $bin_net & $mask ) ) {
				$_SERVER['REMOTE_ADDR'] = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) : '';
				return false;
			}
		}
		return false;
	}
}