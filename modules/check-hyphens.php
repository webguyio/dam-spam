<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_hyphens extends ds_module { 
	public function process( $ip, &$stats=array(), &$options=array(), &$post=array() ) {
		if ( array_key_exists( 'email', $post ) ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				$email = substr( $email, 0, strpos( $email, '@' ) );
				if ( substr_count( $email, "-" ) > 1 ) {
					// translators: %s is the email address with too many hyphens
					return sprintf( esc_html__( 'Too Many Hyphens in: %s', 'dam-spam' ), $email );
				}
			}
		}
		if ( array_key_exists( 'user_email', $post ) ) {
			$email = $post['user_email'];
			if ( !empty( $email ) ) {
				$email = substr( $email, 0, strpos( $email, '@' ) );
				if ( substr_count( $email, "-" ) > 1 ) {
					// translators: %s is the email address with too many hyphens
					return sprintf( esc_html__( 'Too Many Hyphens in: %s', 'dam-spam' ), $email );
				}
			}
		}
		return false;
	}
}

?>
