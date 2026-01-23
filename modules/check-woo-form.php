<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_woo_form extends dam_spam_module {
	// phpcs:disable WordPress.Security.NonceVerification -- Spam detection module intentionally processes untrusted input
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( isset( $_POST['woocommerce-process-checkout-nonce'] ) || isset( $_POST['woocommerce-register-nonce'] ) ) {
			return esc_html__( 'WooCommerce form detected', 'dam-spam' );
		}
		return false;
	}
}

?>