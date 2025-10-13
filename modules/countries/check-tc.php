<?php
// last updated on 4/11/15 04:13:17 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_tc extends ds_module {
	public $searchname = 'Turks and Caicos Islands';
	public $searchlist = array(
		array( '199182192000', '199182196000' )
	);
}

?>