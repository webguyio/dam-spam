<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_spam_words {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$spam_words = $options['spam_words'];
		foreach ( $post as $key => $data ) {
			if ( !empty( $data ) ) {
				foreach ( $spam_words as $sw ) {
					if ( stripos( $data, $sw ) !== false ) {
						// translators: %s is the spam word detected in the content
						return sprintf( esc_html__( 'Spam Word: %1$s in %2$s', 'dam-spam' ), $sw, $key );
					}
				}
			}
		}
		return false;
	}
}

?>
