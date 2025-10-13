<?php
// last updated on 4/11/15 04:13:00 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_pf extends ds_module {
	public $searchname = 'French Polynesia';
	public $searchlist = array(
		array( '123050064000', '123050128000' ),
		array( '202090064000', '202090096000' )
	);
}

?>