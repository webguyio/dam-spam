<?php
// last updated on 4/11/15 04:13:02 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_pm extends ds_module {
	public $searchname = 'Saint Pierre and Miquelon';
	public $searchlist = array(
		array( '070036000000', '070036016000' )
	);
}

?>