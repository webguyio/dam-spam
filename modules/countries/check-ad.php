<?php
// last updated on 4/11/15 04:11:41 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ad extends ds_module {
	public $searchname = 'Andorra';
	public $searchlist = array(
		array( '085094180000', '085094192000' ),
		array( '194158064000', '194158068000' ),
		array( '194158072000', '194158076000' )
	);
}

?>