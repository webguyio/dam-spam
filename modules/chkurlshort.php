<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkurlshort {
	// look on option list for spam words
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		// Checks for links from common URL shortening services
		// 'email', 'author', 'pwd', 'comment', 'subject'
		$blockurlshortners = $options['blockurlshortners'];
		foreach ( $post as $key => $data ) {
			if ( !empty( $data ) ) {
				foreach ( $blockurlshortners as $urlshort ) {
					if ( stripos( $data, $urlshort ) !== false and ( stripos( $data, $urlshort ) == 0 or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == " " or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == "/" or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == "@" or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == "." ) ) {
						sprintf( esc_html__( 'URL Shortener: %1$s in %2$s', 'dam-spam' ), $urlshort, $key );
					}
				}
			}
		}
		return false;
	}
}

?>