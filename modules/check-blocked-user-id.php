<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_blocked_user_id extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Block List User ID';
		$user			  = $post['author'];
		if ( empty( $user ) ) {
			return false;
		}
		$block_list = $options['block_list'];
		return $this->searchList( $user, $block_list );
	}
}

?>