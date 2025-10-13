<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_agent extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( !array_key_exists( 'bad_agents', $options ) ) {
			return false;
		}
		$bad_agents = $options['bad_agents'];
		if ( empty( $bad_agents ) || !is_array( $bad_agents ) ) {
			return false;
		}
		$agent = '';
		if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) ) {
			$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		}
		if ( empty( $agent ) ) {
			return esc_html__( 'Missing User Agent', 'dam-spam' );
		}
		if ( stripos( $agent, 'docs.google.com/viewer' ) !== false ) {
			return false;
		}
		if ( stripos( $agent, '//www.google.com/bot.html)' ) !== false ) {
			return false;
		}
		if ( stripos( $agent, 'bingbot)' ) !== false ) {
			return false;
		}
		foreach ( $bad_agents as $a ) {
			if ( stripos( $agent, $a ) !== false ) {
				return esc_html__( 'Block List User Agent: ', 'dam-spam' ) . $a;
			}
		}
		return false;
	}
}

?>