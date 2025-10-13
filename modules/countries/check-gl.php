<?php
// last updated on 4/11/15 04:12:19 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gl extends ds_module {
	public $searchname = 'Greenland';
	public $searchlist = array(
		array( '088083000000', '088083032000' )
	);
}

?>