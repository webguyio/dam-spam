<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_allow_list_ip extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Allow List IP';
		$gcache		      = $options['allow_list'];
		return $this->searchList( $ip, $gcache );
	}
}

?>