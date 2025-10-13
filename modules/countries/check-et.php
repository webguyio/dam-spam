<?php
// last updated on 4/11/15 04:12:12 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_et extends ds_module {
	public $searchname = 'Ethiopia';
	public $searchlist = array(
		array( '197156067000', '197156068000' )
	);
}

?>