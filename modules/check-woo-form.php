<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_woo_form extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( isset( $_POST['woocommerce-process-checkout-nonce'] ) || isset( $_POST['woocommerce-register-nonce'] ) ) {
			return false;
		}
	}
}

?>