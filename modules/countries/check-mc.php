<?php
// last updated on 4/11/15 04:12:43 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mc extends ds_module {
	public $searchname = 'Monaco';
	public $searchlist = array(
		array( '082113000000', '082113032000' ),
		array( '088209064000', '088209128000' )
	);
}

?>