<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class add_to_good_cache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		extract( $stats );
		extract( $options );
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		while ( count( $goodips ) > $ds_good ) {
			array_shift( $goodips );
		}
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 4 * 3600 ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		$goodips[$ip] = $now;
		foreach ( $goodips as $key => $data ) {
			if ( $data < $nowtimeout ) {
				unset( $goodips[$key] );
			}
		}
		$stats['goodips'] = $goodips;
		if ( array_key_exists( $ip, $stats['badips'] ) ) {
			unset( $stats['badips'] );
		}
		ds_set_stats( $stats );
		return $goodips;
	}
}

?>