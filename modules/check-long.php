<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_long {
	public $searchname = 'Email/Username/Password Too Long';
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( array_key_exists( 'email', $post ) ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				if ( strlen( $email ) > 64 ) {
					// translators: %s is the email address that's too long
					return sprintf( esc_html__( 'Email Too Long: %s', 'dam-spam' ), $email );
				}
			}
		}
		if ( array_key_exists( 'author', $post ) ) {
			if ( !empty( $post['author'] ) ) {
				$author = $post['author'];
				if ( strlen( $post['author'] ) > 64 ) {
					// translators: %s is the username that's too long
					return sprintf( esc_html__( 'Username Too Long: %s', 'dam-spam' ), $author );
				}
			}
		}
		if ( array_key_exists( 'psw', $post ) ) {
			if ( !empty( $post['psw'] ) ) {
				$psw = $post['psw'];
				if ( strlen( $post['psw'] ) > 32 ) {
					// translators: %s is the password that's too long
					return sprintf( esc_html__( 'Password Too Long: %s', 'dam-spam' ), $psw );
				}
			}
		}
		return false;
	}
}

?>
