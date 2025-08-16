<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkshort { // change name
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Email/Username Too Short';
		if ( array_key_exists( 'email', $post ) ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				if ( strlen( $email ) < 5 ) {
					sprintf( esc_html__( 'Email Too Short: %s', 'dam-spam' ), $email );
				}
			}
		}
		if ( array_key_exists( 'author', $post ) ) {
			if ( !empty( $post['author'] ) ) {
				$author = $post['author'];
				// short author is OK?
				if ( strlen( $post['author'] ) < 3 ) {
					sprintf( esc_html__( 'Username Too Short: %s', 'dam-spam' ), $author );
				}
			}
		}
		return false;
	}
}

?>