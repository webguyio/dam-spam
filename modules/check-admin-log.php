<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_admin_log extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$sname = $this->getSname();
		if ( !class_exists( 'GoogleAuthenticator' )
			 && strpos( $sname, 'wp-login.php' ) !== false
			 && function_exists( 'wp_authenticate' )
		) {
			$log = $post['author'];
			$pwd = $post['pwd'];
			if ( empty( $log ) || empty( $pwd ) ) {
				return false;
			}
			$user = @wp_authenticate( $log, $pwd );
			if ( !is_wp_error( $user ) ) {
				return esc_html__( 'Authenticated User Login', 'dam-spam' );
			}
			return false;
		}
		return false;
	}
}

?>