<?php
// last updated on 4/11/15 04:12:02 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_cm extends ds_module {
	public $searchname = 'Cameroon';
	public $searchlist = array(
		array( '169255004000', '169255008000' ),
		array( '195024192000', '195024224000' ),
		array( '197159000000', '197159032000' )
	);
}

?>