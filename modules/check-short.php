<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_short {
	public $searchname = 'Email/Username Too Short';
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( array_key_exists( 'email', $post ) ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				if ( strlen( $email ) < 5 ) {
					// translators: %s is the email address that's too short
					return sprintf( esc_html__( 'Email Too Short: %s', 'dam-spam' ), $email );
				}
			}
		}
		if ( array_key_exists( 'author', $post ) ) {
			if ( !empty( $post['author'] ) ) {
				$author = $post['author'];
				if ( strlen( $post['author'] ) < 3 ) {
					// translators: %s is the username that's too short
					return sprintf( esc_html__( 'Username Too Short: %s', 'dam-spam' ), $author );
				}
			}
		}
		return false;
	}
}

?>
