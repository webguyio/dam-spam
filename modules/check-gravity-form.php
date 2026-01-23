<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_gravity_form extends dam_spam_module {
	// phpcs:disable WordPress.Security.NonceVerification -- Spam detection module intentionally processes untrusted input
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( isset( $_POST["gform_submit"] ) ) {
			return esc_html__( 'Gravity Forms submission detected', 'dam-spam' );
		}
		return false;
	}
}

?>