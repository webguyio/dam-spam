<?php
// last updated on 4/11/15 04:12:53 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mz extends ds_module {
	public $searchname = 'Mozambique';
	public $searchlist = array(
		array( '197218192000', '197218224000' ),
		array( '197235032000', '197235064000' ),
		array( '197249064000', '197249128000' )
	);
}

?>