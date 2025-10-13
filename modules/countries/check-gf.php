<?php
// last updated on 4/11/15 04:12:17 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gf extends ds_module {
	public $searchname = 'French Guiana';
	public $searchlist = array(
		array( '161022064000', '161022128000' )
	);
}

?>