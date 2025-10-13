<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class log_bad extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$check = 'error';
		extract( $stats );
		extract( $post );
		$sname = $this->getSname();
		$now   = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		if ( array_key_exists( 'spam_count', $stats ) ) {
			$stats['spam_count'] ++;
		} else {
			$stats['spam_count'] = 1;
		}
		if ( array_key_exists( 'spam_multisite_count', $stats ) ) {
			$stats['spam_multisite_count'] ++;
		} else {
			$stats['spam_multisite_count'] = 1;
		}
		if ( array_key_exists( 'count_' . $check, $stats ) ) {
			$stats['count_' . $check] ++;
		} else {
			$stats['count_' . $check] = 1;
		}
		$ds_cache	 = $options['ds_cache'];
		$badips[$ip] = $now;
		asort( $badips );
		while ( count( $badips ) > $ds_cache ) {
			array_shift( $badips );
		}
		$nowtimeout = gmdate( 'Y/m/d H:i:s', time() - ( 4 * 3600 ) + ( get_option( 'gmt_offset' ) * 3600 ) );
		foreach ( $badips as $key => $data ) {
			if ( $data < $nowtimeout ) {
				unset( $badips[$key] );
			}
		}
		$stats['badips'] = $badips;
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
		$hist[$now]	   = array( $ip, $email, $author, $sname, $reason, $blog );
		$stats['hist'] = $hist;
		if ( array_key_exists( 'addon', $post ) ) {
			ds_set_stats( $stats, $post['addon'] );
		} else {
			ds_set_stats( $stats );
		}
		do_action( 'ds_caught', $ip, $post );
		ds_load( 'challenge', $ip, $stats, $options, $post );
		exit();
	}
}

?>