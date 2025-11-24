<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_url_short {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$block_url_shortners = $options['block_url_shortners'];
		foreach ( $post as $key => $data ) {
			if ( !empty( $data ) ) {
				foreach ( $block_url_shortners as $urlshort ) {
					if ( stripos( $data, $urlshort ) !== false and ( stripos( $data, $urlshort ) == 0 or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == " " or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == "/" or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == "@" or substr( $data, stripos( $data, $urlshort ) - 1, 1 ) == "." ) ) {
						// translators: %s is the shortened URL detected
						return sprintf( esc_html__( 'URL Shortener: %1$s in %2$s', 'dam-spam' ), $urlshort, $key );
					}
				}
			}
		}
		return false;
	}
}

?>
