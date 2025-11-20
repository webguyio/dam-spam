<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_wp_form extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( isset( $_POST['wpforms'] ) ) {
			return 'WP Forms submission detected';
		}
		return false;
	}
}

?>