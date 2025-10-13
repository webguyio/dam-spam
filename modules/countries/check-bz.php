<?php
// last updated on 4/11/15 04:11:57 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bz extends ds_module {
	public $searchname = 'Belize';
	public $searchlist = array(
		array( '031220000000', '031220004000' ),
		array( '191097080000', '191097088000' ),
		array( '200123208000', '200123216000' )
	);
}

?>