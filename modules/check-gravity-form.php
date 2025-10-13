<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gravity_form extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( isset( $_POST["gform_submit"] ) ) {
			return false;
		}
	}
}

?>