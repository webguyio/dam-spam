<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_good_cache extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Good Cache';
		$gcache		      = $stats['goodips'];
		return $this->searchcache( $ip, $gcache );
	}
}

?>