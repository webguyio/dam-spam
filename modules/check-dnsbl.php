<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_dnsbl {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( strpos( $ip, '.' ) === false ) {
			return false;
		}
		$iplist = array(
			'sbl.spamhaus' => '.sbl.spamhaus.org',
			'xbl.spamhaus' => '.xbl.spamhaus.org'
		);
		foreach ( $iplist as $data ) {
			$lookup = implode( '.', array_reverse( explode( '.', $ip ) ) ) . $data;
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
		}
		return false;
	}
}

?>