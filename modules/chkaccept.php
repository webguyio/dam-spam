<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkaccept {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( array_key_exists( 'HTTP_ACCEPT', $_SERVER ) ) {
			return false;
		} // real browsers send HTTP_ACCEPT
		return esc_html__( 'No Accept Header: ', 'dam-spam' );
	}
}

?>