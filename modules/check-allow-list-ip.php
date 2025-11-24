<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_allow_list_ip extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Allow List IP';
		$gcache		      = $options['allow_list'];
		return $this->searchList( $ip, $gcache );
	}
}

?>