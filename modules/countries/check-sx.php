<?php
// last updated on 4/11/15 04:13:16 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_sx extends ds_module {
	public $searchname = 'Sint Maarten';
	public $searchlist = array(
		array( '190102000000', '190102032000' ),
		array( '190124216000', '190124220000' ),
		array( '190185080000', '190185088000' ),
		array( '201220000000', '201220016000' )
	);
}

?>