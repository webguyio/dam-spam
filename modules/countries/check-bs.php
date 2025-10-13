<?php
// last updated on 4/11/15 04:11:55 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bs extends ds_module {
	public $searchname = 'Bahamas';
	public $searchlist = array(
		array( '024051064000', '024051128000' ),
		array( '204236064000', '204236128000' )
	);
}

?>