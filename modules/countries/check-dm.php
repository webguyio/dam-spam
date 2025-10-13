<?php
// last updated on 4/11/15 04:12:08 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_dm extends ds_module {
	public $searchname = 'Dominica';
	public $searchlist = array(
		array( '199127196000', '199127200000' )
	);
}

?>