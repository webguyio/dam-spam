<?php
// last updated on 4/11/15 04:12:46 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ml extends ds_module {
	public $searchname = 'Mali';
	public $searchlist = array(
		array( '197155152000', '197155160000' )
	);
}

?>