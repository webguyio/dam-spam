<?php
// last updated on 4/11/15 04:12:20 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gp extends ds_module {
	public $searchname = 'Guadeloupe';
	public $searchlist = array(
		array( '093121128000', '093122000000' ),
		array( '107191208000', '107191224000' )
	);
}

?>