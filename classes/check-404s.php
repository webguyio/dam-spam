<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_404s {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( $options['check_404'] !== 'Y' ) {
			return false;
		}
		$reason = ds_load( 'check_404', $ip, $stats, $options, $post );
		if ( $reason === false ) {
			return;
		}
		ds_log_bad( $ip, $reason, 'check_404' );
		$reject_message = $options['reject_message'];
		wp_die( esc_html( $reject_message ), esc_html__( 'Login Access Blocked', 'dam-spam' ), array( 'response' => 403 ) );
		exit();
	}
}

?>