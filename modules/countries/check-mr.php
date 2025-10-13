<?php
// last updated on 4/11/15 04:12:49 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mr extends ds_module {
	public $searchname = 'Mauritania';
	public $searchlist = array(
		array( '197231000000', '197231032000' )
	);
}

?>