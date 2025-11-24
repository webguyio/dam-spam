<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_add_to_block_list {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		$block_list = $options['block_list'];
		if ( !in_array( $ip, $block_list ) ) {
			$block_list[] = $ip;
		}
		$options['block_list'] = $block_list;
		dam_spam_set_options( $options );
		$badips = $stats['badips'];
		if ( array_key_exists( $ip, $badips ) ) {
			unset( $badips[$ip] );
			$stats['badips'] = $badips;
		}
		$goodips = $stats['goodips'];
		if ( array_key_exists( $ip, $goodips ) ) {
			unset( $goodips[$ip] );
			$stats['goodips'] = $goodips;
		}
		dam_spam_set_stats( $stats );
		return false;
	}
}

?>