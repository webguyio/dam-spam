<?php
// last updated on 4/11/15 04:12:00 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_cf extends ds_module {
	public $searchname = 'Central African Republic';
	public $searchlist = array(
		array( '193251128000', '193251160000' )
	);
}

?>