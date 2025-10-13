<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_honeypot {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( strpos( $ip, '.' ) === false ) {
			return false;
		}
		if ( empty( $stats ) ) {
			return false;
		}
		if ( !array_key_exists( 'honeyapi', $stats ) ) {
			return false;
		}
		$apikey = $stats['honeyapi'];
		$data   = '.dnsbl.httpbl.org';
		$lookup = implode( '.', array_reverse( explode( '.', $ip ) ) ) . $data;
		$lookup = $apikey . '.' . $lookup;
		$result = explode( '.', gethostbyname( $lookup ) );
		$retip  = $ip;
		if ( count( $result ) == 4 ) {
			$retip = $result[3] . '.' . $result[2] . '.' . $result[1] . '.' . $result[0];
		}
		if ( count( $result ) == 4 && $retip != $ip ) {
			if ( $result[0] == 127 ) {
				if ( $result[2] >= 25 && ( $result[3] >= 1 && $result[3] <= 7 ) && $result[1] > 0 ) {
					return "DNSBL: $data=" . $result[0] . ',' . $result[1] . ',' . $result[2] . ',' . $result[3];
				}
			}
		}
		return false;
	}
}

?>