<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_log_bad extends dam_spam_module {
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
		$dam_spam_cache	 = $options['dam_spam_cache'];
		$badips[$ip] = $now;
		asort( $badips );
		while ( count( $badips ) > $dam_spam_cache ) {
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
		$dam_spam_hist = $options['dam_spam_hist'];
		while ( count( $hist ) > $dam_spam_hist ) {
			array_shift( $hist );
		}
		$hist[$now]	   = array( $ip, $email, $author, $sname, $reason, $blog );
		$stats['hist'] = $hist;
		if ( array_key_exists( 'addon', $post ) ) {
			dam_spam_set_stats( $stats, $post['addon'] );
		} else {
			dam_spam_set_stats( $stats );
		}
		do_action( 'dam_spam_caught', $ip, $post );
		dam_spam_load( 'challenge', $ip, $stats, $options, $post );
		exit();
	}
}

?>