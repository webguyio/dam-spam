<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_add_to_bad_cache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		extract( $stats );
		extract( $options );
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		while ( count( $badips ) > $dam_spam_cache ) {
			array_shift( $badips );
		}
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 4 * 3600 ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		$badips[$ip] = $now;
		foreach ( $badips as $key => $data ) {
			if ( $data < $nowtimeout ) {
				unset( $badips[$key] );
			}
		}
		$stats['badips'] = $badips;
		dam_spam_set_stats( $stats );
		return $badips;
	}
}

?>