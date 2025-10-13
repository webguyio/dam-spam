<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class log_good extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$check = "error";
		extract( $stats );
		extract( $post );
		if ( array_key_exists( 'count_' . $check, $stats ) ) {
			$stats['count_' . $check] ++;
		} else {
			$stats['count_' . $check] = 1;
		}
		$sname = $this->getSname();
		$now   = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		if ( array_key_exists( 'count_pass', $stats ) ) {
			$stats['count_pass'] ++;
		} else {
			$stats['count_pass'] = 1;
		}
		$ds_good	  = $options['ds_good'];
		$goodips[$ip] = $now;
		asort( $goodips );
		while ( count( $goodips ) > $ds_good ) {
			array_shift( $goodips );
		}
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 4 * 3600 ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		foreach ( $goodips as $key => $data ) {
			if ( $data < $nowtimeout ) {
				unset( $goodips[$key] );
			}
		}
		$stats['goodips'] = $goodips;
		$blog = '';
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $blog_id;
			if ( !isset( $blog_id ) || $blog_id != 1 ) {
				$blog = $blog_id;
			}
		}
		$ds_hist = $options['ds_hist'];
		while ( count( $hist ) > $ds_hist ) {
			array_shift( $hist );
		}
		$hist[$now]  = array( $ip, $email, $author, $sname, $reason, $blog );
		$stats['hist'] = $hist;
		if ( array_key_exists( 'addon', $post ) ) {
			ds_set_stats( $stats, $post['addon'] );
		} else {
			ds_set_stats( $stats );
		}
	}
}

?>