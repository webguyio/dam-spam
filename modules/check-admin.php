<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_admin extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$login = $post['author'];
		$pwd   = $post['pwd'];
		if ( stripos( $login, 'admin' ) === false ) {
			return false;
		}
		if ( !function_exists( 'get_users' ) ) {
			return false;
		}
		if ( get_user_by( 'login', $login ) ) {
			return false;
		}
		// translators: %s is the admin username detected
		return sprintf( esc_html__( 'Admin Login or Registration Attempt: %s', 'dam-spam' ), $login );
	}
}

?>
