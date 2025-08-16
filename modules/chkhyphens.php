<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkhyphens extends be_module { 
	public function process( $ip, &$stats=array(), &$options=array(), &$post=array() ) {
		if ( array_key_exists( 'email', $post ) ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				$email = substr( $email, 0, strpos( $email, '@' ) );
				if ( substr_count( $email, "-" ) > 1 ) {
					sprintf( esc_html__( 'Too many hyphens in: %s', 'dam-spam' ), $email );
				}
			}
		}
		if ( array_key_exists( 'user_email', $post ) ) {
			$email = $post['user_email'];
			if ( !empty( $email ) ) {
				$email = substr( $email, 0, strpos( $email, '@' ) );
				if ( substr_count( $email, "-" ) > 1 ) {
					sprintf( esc_html__( 'Too many hyphens in: %s', 'dam-spam' ), $email );
				}
			}
		}
		return false;
	}
}

?>