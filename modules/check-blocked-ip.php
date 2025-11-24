<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_blocked_ip extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Block List IP';
		$gcache		      = $options['block_list'];
		return $this->searchList( $ip, $gcache );
	}
}

?>