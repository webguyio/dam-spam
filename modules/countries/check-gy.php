<?php
// last updated on 4/11/15 04:12:24 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gy extends ds_module {
	public $searchname = 'Guyana';
	public $searchlist = array(
		array( '190080000000', '190080128000' ),
		array( '190093036000', '190093040000' ),
		array( '190108200000', '190108208000' )
	);
}

?>