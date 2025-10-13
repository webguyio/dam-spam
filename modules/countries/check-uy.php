<?php
// last updated on 4/11/15 04:13:27 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_uy extends ds_module {
	public $searchname = 'Uruguay';
	public $searchlist = array(
		array( '167056000000', '167064000000' ),
		array( '179024000000', '179032000000' ),
		array( '186048000000', '186056000000' ),
		array( '190108024000', '190108028000' ),
		array( '190132000000', '190136000000' ),
		array( '200035153128', '200035154000' ),
		array( '200040000000', '200040128000' ),
		array( '200058129200', '200058129208' ),
		array( '201221047032', '201221047040' )
	);
}

?>