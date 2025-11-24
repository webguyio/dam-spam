<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_multi extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( function_exists( 'is_user_logged_in' ) ) {
			if ( is_user_logged_in() ) {
				return false;
			}
		}
		if ( !array_key_exists( 'multi', $stats ) ) {
			return false;
		}
		$multi = $stats['multi'];
		if ( !is_array( $multi ) ) {
			$multi = array();
		}
		$multitime = 3;
		$multicount  = 5;
		if ( array_key_exists( 'multitime', $options ) ) {
			$multitime = $options['multitime'];
		}
		if ( array_key_exists( 'multicount', $options ) ) {
			$multicount = $options['multicount'];
		}
		$now		= gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 60 * $multitime ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		foreach ( $multi as $key => $data ) {
			if ( $data[0] < $nowtimeout ) {
				unset( $multi[$key] );
			}
		}
		$row = array( $now, 0 );
		if ( array_key_exists( $ip, $multi ) ) {
			$row = $multi[$ip];
		}
		$row[0] = $now;
		$row[1] ++;
		$multi[$ip]   = $row;
		$stats['multi'] = $multi;
		dam_spam_set_stats( $stats );
		if ( $row[1] >= $multicount ) {
			// translators: %s is the hit count value
			return sprintf( esc_html__( '%s hits in last 3 minutes', 'dam-spam' ), $row[1] );
		}
		return false;
	}
}

?>
