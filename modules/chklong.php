<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chklong { // change name
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Email/Username/Password Too Long';
		if ( array_key_exists( 'email', $post ) ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				if ( strlen( $email ) > 64 ) {
					sprintf( esc_html__( 'Email Too Long: %s', 'dam-spam' ), $email );
				}
			}
		}
		if ( array_key_exists( 'author', $post ) ) {
			if ( !empty( $post['author'] ) ) {
				$author = $post['author'];
				if ( strlen( $post['author'] ) > 64 ) {
					sprintf( esc_html__( 'Username Too Long: %s', 'dam-spam' ), $author );
				}
			}
		}
		if ( array_key_exists( 'psw', $post ) ) {
			if ( !empty( $post['psw'] ) ) {
				$psw = $post['psw'];
				if ( strlen( $post['psw'] ) > 32 ) {
					sprintf( esc_html__( 'Password Too Long: %s', 'dam-spam' ), $psw );
				}
			}
		}
		return false;
	}
}

?>