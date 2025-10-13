<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_scripts extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$sname = $this->getSname();
		if ( strpos( $sname, 'wp-cron.php' ) !== false ) {
			return esc_html__( 'allow wp-cron', 'dam-spam' );
		}
		if ( strpos( $sname, 'admin-ajax.php' ) !== false ) {
			return esc_html__( 'allow admin-ajax.php', 'dam-spam' );
		}
		return false;
	}
}

?>