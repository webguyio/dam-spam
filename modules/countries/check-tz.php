<?php
// last updated on 4/11/15 04:13:24 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_tz extends ds_module {
	public $searchname = 'Tanzania';
	public $searchlist = array(
		array( '156156000000', '156158000000' ),
		array( '188164032000', '188164064000' ),
		array( '197250000000', '197251000000' )
	);
}

?>