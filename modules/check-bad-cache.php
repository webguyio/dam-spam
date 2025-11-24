<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_bad_cache extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$this->searchname = 'Bad Cache';
		$gcache		      = $stats['badips'];
		return $this->searchcache( $ip, $gcache );
	}
}

?>