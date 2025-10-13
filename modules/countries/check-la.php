<?php
// last updated on 4/11/15 04:12:38 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_la extends ds_module {
	public $searchname = 'Laos';
	public $searchlist = array(
		array( '103240240000', '103240244000' ),
		array( '183182112000', '183182128000' ),
		array( '202062096000', '202062112000' ),
		array( '202137128000', '202137160000' )
	);
}

?>