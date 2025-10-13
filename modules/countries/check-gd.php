<?php
// last updated on 4/11/15 04:12:16 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gd extends ds_module {
	public $searchname = 'Grenada';
	public $searchlist = array(
		array( '074117084000', '074117088000' ),
		array( '162245152000', '162245156000' )
	);
}

?>