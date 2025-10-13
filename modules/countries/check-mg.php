<?php
// last updated on 4/11/15 04:12:45 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mg extends ds_module {
	public $searchname = 'Madagascar';
	public $searchlist = array(
		array( '197149000000', '197149064000' ),
		array( '197158064000', '197158128000' )
	);
}

?>