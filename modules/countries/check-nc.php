<?php
// last updated on 4/11/15 04:12:54 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_nc extends ds_module {
	public $searchname = 'New Caledonia';
	public $searchlist = array(
		array( '175158128000', '175158192000' ),
		array( '203147064000', '203147065000' )
	);
}

?>