<?php
// last updated on 4/11/15 04:13:14 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_sr extends ds_module {
	public $searchname = 'Suriname';
	public $searchlist = array(
		array( '186179128000', '186180000000' ),
		array( '190098000000', '190098128000' ),
		array( '200001208000', '200001216000' )
	);
}

?>