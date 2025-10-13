<?php
// last updated on 4/11/15 04:13:04 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_pw extends ds_module {
	public $searchname = 'Palau';
	public $searchlist = array(
		array( '103251132000', '103251134000' )
	);
}

?>