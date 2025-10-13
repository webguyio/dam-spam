<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_site_get extends ds_module {
	public function process(
		$ip, &$stats = array(), &$options = array(), &$post = array() ) {
		return false;
	}
}

?>