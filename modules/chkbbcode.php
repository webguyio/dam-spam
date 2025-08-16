<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkbbcode { // change name
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		// searches for BBCodes in post data
		// BBCodes is the tool of common spammers
		$bbcodes = array(
			'[php',
			'[url',
			'[link',
			'[img',
			'[include',
			'[script'
		);
		foreach ( $post as $key => $data ) {
			foreach ( $bbcodes as $bb ) {
				// sfs_debug_msg( "looking for $key - $bb in $data" );
				if ( stripos( $data, $bb ) !== false ) {
					sprintf( esc_html__( 'BBCode %1$s in %2$s', 'dam-spam' ), $bb, $key );
				}
			}
		}
		return false;
	}
}

?>