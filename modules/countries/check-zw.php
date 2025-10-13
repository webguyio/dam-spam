<?php
// last updated on 4/11/15 04:13:34 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_zw extends ds_module {
	public $searchname = 'Zimbabwe';
	public $searchlist = array(
		array( '197211208000', '197211216000' )
	);
}

?>