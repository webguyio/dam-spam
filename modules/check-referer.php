<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_referer extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'HTTP_REFERER check';
		if ( !isset( $_SERVER['REQUEST_METHOD'] ) || sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) !== 'POST' ) {
			return false;
		}
		$ref = '';
		if ( array_key_exists( 'HTTP_REFERER', $_SERVER ) ) {
			$ref = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		}
		$ua = '';
		if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) ) {
			$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		}
		$a = array( false, '' );
		if ( strpos( strtolower( $ua ), 'iphone' ) === false && strpos( strtolower( $ua ), 'ipad' ) === false ) {
			return false;
		}
		$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		if ( empty( $ref ) ) {
			return esc_html__( 'Missing HTTP_REFERER', 'dam-spam' );
		}
		if ( empty( $host ) ) {
			return esc_html__( 'Missing HTTP_HOST', 'dam-spam' );
		}
		if ( empty( $ref ) ) {
			return false;
		}
		if ( strpos( strtolower( $ref ), strtolower( $host ) ) === false ) {
			return esc_html__( 'Invalid HTTP_REFERER', 'dam-spam' );
		}
		return false;
	}
}

?>