<?php
// last updated on 4/11/15 04:12:23 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gu extends ds_module {
	public $searchname = 'Guam';
	public $searchlist = array(
		array( '101099128000', '101100000000' ),
		array( '103007100000', '103007104000' ),
		array( '121055192000', '121056000000' ),
		array( '182173192000', '182174000000' ),
		array( '202128000000', '202128032000' )
	);
}

?>