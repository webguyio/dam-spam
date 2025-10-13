<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_form extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( strpos( $uri, 'wp-comments-post.php' ) !== false ) {
			return false;
		}
		if ( strpos( $uri, 'wp-login.php' ) !== false ) {
			return false;
		}
		// translators: %s is the URI that's not a standard form
		return sprintf( esc_html__( 'Post request not in wp-comments-post.php or wp-login.php — %s', 'dam-spam' ), $uri );
	}
}

?>
