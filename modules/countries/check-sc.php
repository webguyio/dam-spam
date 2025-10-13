<?php
// last updated on 4/11/15 04:13:09 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_sc extends ds_module {
	public $searchname = 'Seychelles';
	public $searchlist = array(
		array( '193107017000', '193107018000' ),
		array( '193107019000', '193107020000' )
	);
}

?>