<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_remove_good_cache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		extract( $stats );
		extract( $options );
		while ( count( $goodips ) > $dam_spam_good ) {
			array_shift( $goodips );
		}
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 4 * 3600 ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		foreach ( $goodips as $key => $data ) {
			if ( $data < $nowtimeout ) {
				unset( $goodips[$key] );
			}
			if ( $key == $ip ) {
				unset( $goodips[$key] );
			}
		}
		$stats['goodips'] = $goodips;
		dam_spam_set_stats( $stats );
		return $goodips;
	}
}

?>