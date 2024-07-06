<?php
// this checks 404 entries for attacks

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class ds_check_404s {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		// load the chk404 module
		if ( $options['chk404'] !== 'Y' ) {
			return false;
		}
		$reason = be_load( 'chk404', $ip );
		if ( $reason === false ) {
			return;
		}
		// update log
		ds_log_bad( $ip, $reason, 'chk404' );
		// need to block access
		$rejectmessage = $options['rejectmessage'];
		wp_die( '$rejectmessage', __( 'Login Access Blocked', 'dam-spam' ), array( 'response' => 403 ) );
		exit();
	}
}

?>