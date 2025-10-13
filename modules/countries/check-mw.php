<?php
// last updated on 4/11/15 04:12:51 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mw extends ds_module {
	public $searchname = 'Malawi';
	public $searchlist = array(
		array( '105234128000', '105235000000' )
	);
}

?>