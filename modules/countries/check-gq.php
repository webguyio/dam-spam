<?php
// last updated on 4/11/15 04:12:21 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gq extends ds_module {
	public $searchname = 'Equatorial Guinea';
	public $searchlist = array(
		array( '105235224000', '105235240000' ),
		array( '197214064000', '197214080000' )
	);
}

?>