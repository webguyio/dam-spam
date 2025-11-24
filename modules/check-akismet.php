<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_akismet {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( !function_exists( 'get_option' ) ) {
			return false;
		}
		if ( !function_exists( 'site_url' ) ) {
			return false;
		}
		$api_key = get_option( 'wordpredam_spam_api_key' );
		if ( empty( $api_key ) ) {
			return false;
		}
		$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		$refer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		$blogurl = site_url();
		$api_key = sanitize_text_field( $api_key );
		if ( empty( $api_key ) || empty( $agent ) || empty( $blogurl ) ) {
			return false;
		}
		$data = array(
			'blog' => $blogurl,
			'user_ip' => sanitize_text_field( $ip ),
			'user_agent' => $agent,
			'referrer' => $refer,
			'permalink' => '',
			'comment_type' => 'comment',
			'comment_author' => '',
			'comment_author_email' => '',
			'comment_author_url' => '',
			'comment_content' => ''
		);
		$response = $this->akismet_comment_check( $api_key, $data );
		return $response;
	}
	function akismet_comment_check( $key, $data ) {
		$request = 'blog=' . urlencode( $data['blog'] ) .
				   '&user_ip=' . urlencode( $data['user_ip'] ) .
				   '&user_agent=' . urlencode( $data['user_agent'] ) .
				   '&referrer=' . urlencode( $data['referrer'] ) .
				   '&permalink=' . urlencode( $data['permalink'] ) .
				   '&comment_type=' . urlencode( $data['comment_type'] ) .
				   '&comment_author=' . urlencode( $data['comment_author'] ) .
				   '&comment_author_email=' . urlencode( $data['comment_author_email'] ) .
				   '&comment_author_url=' . urlencode( $data['comment_author_url'] ) .
				   '&comment_content=' . urlencode( $data['comment_content'] );
		$host = sanitize_text_field( $key ) . '.rest.akismet.com';
		$path = '/1.1/comment-check';
		$akismet_ua = sprintf( 'WordPress/%s | Akismet/%s', $GLOBALS['wp_version'], constant( 'AKISMET_VERSION' ) );
		$args = array(
			'body' => $request,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'User-Agent' => $akismet_ua,
			),
			'timeout' => 10,
		);
		$response = wp_remote_post( 'https://' . $host . $path, $args );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$body = wp_remote_retrieve_body( $response );
		if ( 'true' === trim( $body ) ) {
			return $body;
		}
		return $body;
	}
}

?>