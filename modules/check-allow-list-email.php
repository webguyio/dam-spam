<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_allow_list_email extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( is_user_logged_in() ) {
			$current_user 	  = wp_get_current_user();
			$this->searchname = 'Allow List Email';
			$gcache		      = $options['allow_list_email'];
			return $this->searchList( $current_user->user_email, $gcache );
		}
		return false;
	}
}

?>