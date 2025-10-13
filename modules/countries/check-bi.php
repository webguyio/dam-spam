<?php
// last updated on 4/11/15 04:11:52 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bi extends ds_module {
	public $searchname = 'Burundi';
	public $searchlist = array(
		array( '197231248000', '197231252000' )
	);
}

?>