<?php
// last updated on 4/11/15 04:13:21 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_tm extends ds_module {
	public $searchname = 'Turkmenistan';
	public $searchlist = array(
		array( '217174224000', '217174225000' ),
		array( '217174233000', '217174235000' )
	);
}

?>