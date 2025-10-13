<?php
// last updated on 4/11/15 04:11:48 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ax extends ds_module {
	public $searchname = 'Åland';
	public $searchlist = array(
		array( '079133000000', '079133032000' ),
		array( '194112000000', '194112016000' )
	);
}

?>