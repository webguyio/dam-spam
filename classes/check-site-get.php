<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_site_get extends dam_spam_module {
	public function process(
		$ip, &$stats = array(), &$options = array(), &$post = array() ) {
		return false;
	}
}

?>