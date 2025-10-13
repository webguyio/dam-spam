<?php
// last updated on 4/11/15 04:11:45 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ao extends ds_module {
	public $searchname = 'Angola';
	public $searchlist = array(
		array( '105168000000', '105176000000' ),
		array( '197217064000', '197217128000' )
	);
}

?>