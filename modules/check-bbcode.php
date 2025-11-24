<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_bbcode {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
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
				if ( stripos( $data, $bb ) !== false ) {
					// translators: %s is the BBCode found in the submission
					return sprintf( esc_html__( 'BBCode %1$s in %2$s', 'dam-spam' ), $bb, $key );
				}
			}
		}
		return false;
	}
}

?>
