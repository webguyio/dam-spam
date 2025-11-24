<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_blocked_email extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Block List Email';
		$email			  = $post['email'];
		if ( empty( $email ) ) {
			return false;
		}
		$block_list = $options['block_list'];
		return $this->searchList( $email, $block_list );
	}
}

?>