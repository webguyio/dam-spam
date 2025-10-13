<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class remove_bad_cache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		extract( $stats );
		extract( $options );
		while ( count( $badips ) > $ds_cache ) {
			array_shift( $badips );
		}
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 4 * 3600 ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		foreach ( $badips as $key => $data ) {
			if ( $data < $nowtimeout ) {
				unset( $badips[$key] );
			}
			if ( $key == $ip ) {
				unset( $badips[$key] );
			}
		}
		$stats['badips'] = $badips;
		ds_set_stats( $stats );
		return $badips;
	}
}

?>