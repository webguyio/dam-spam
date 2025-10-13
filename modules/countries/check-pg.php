<?php
// last updated on 4/11/15 04:13:00 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_pg extends ds_module {
	public $searchname = 'Papua New Guinea';
	public $searchlist = array(
		array( '103009226000', '103009227000' ),
		array( '103014088000', '103014092000' ),
		array( '103016156000', '103016160000' ),
		array( '103232142000', '103232144000' )
	);
}

?>