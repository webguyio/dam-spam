<?php
// last updated on 4/11/15 04:12:51 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mu extends ds_module {
	public $searchname = 'Mauritius';
	public $searchlist = array(
		array( '197155064000', '197155096000' ),
		array( '197226000000', '197228000000' )
	);
}

?>