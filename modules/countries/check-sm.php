<?php
// last updated on 4/11/15 04:13:13 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_sm extends ds_module {
	public $searchname = 'San Marino';
	public $searchlist = array(
		array( '079099192000', '079099200000' )
	);
}

?>