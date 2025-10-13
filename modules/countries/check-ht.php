<?php
// last updated on 4/11/15 04:12:26 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ht extends ds_module {
	public $searchname = 'Haiti';
	public $searchlist = array(
		array( '186001192000', '186001208000' ),
		array( '186190000000', '186190128000' ),
		array( '190102064000', '190102096000' ),
		array( '190115128000', '190115192000' ),
		array( '200004160000', '200004192000' ),
		array( '200113196000', '200113197000' ),
		array( '200113219000', '200113220000' ),
		array( '200113221000', '200113222000' )
	);
}

?>