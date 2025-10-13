<?php
// last updated on 4/11/15 04:13:07 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_rw extends ds_module {
	public $searchname = 'Rwanda';
	public $searchlist = array(
		array( '105178000000', '105180000000' ),
		array( '197243000000', '197243128000' )
	);
}

?>