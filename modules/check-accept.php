<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_accept {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( array_key_exists( 'HTTP_ACCEPT', $_SERVER ) ) {
			return false;
		}
		return esc_html__( 'No Accept Header: ', 'dam-spam' );
	}
}

?>