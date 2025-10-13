<?php
// last updated on 4/11/15 04:11:55 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bq extends ds_module {
	public $searchname = 'Bonaire';
	public $searchlist = array(
		array( '190107248000', '190108000000' )
	);
}

?>