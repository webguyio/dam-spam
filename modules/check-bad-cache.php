<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bad_cache extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Bad Cache';
		$gcache		      = $stats['badips'];
		return $this->searchcache( $ip, $gcache );
	}
}

?>