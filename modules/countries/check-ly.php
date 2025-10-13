<?php
// last updated on 4/11/15 04:12:42 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ly extends ds_module {
	public $searchname = 'Libya';
	public $searchlist = array(
		array( '005063000000', '005063004000' ),
		array( '062068032000', '062068064000' )
	);
}

?>