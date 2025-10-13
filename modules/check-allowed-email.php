<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_allowed_email extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Allow List Email';
		$email			  = $post['email'];
		if ( empty( $email ) ) {
			return false;
		}
		$allow_list = $options['allow_list'];
		return $this->searchList( $email, $allow_list );
	}
}

?>