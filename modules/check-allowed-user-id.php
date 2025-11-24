<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_allowed_user_id extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Allow List Email';
		$user			  = $post['author'];
		if ( empty( $user ) ) {
			return false;
		}
		$allow_list = $options['allow_list'];
		return $this->searchList( $user, $allow_list );
	}
}

?>