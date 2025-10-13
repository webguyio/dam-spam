<?php
// last updated on 4/11/15 04:13:25 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ug extends ds_module {
	public $searchname = 'Uganda';
	public $searchlist = array(
		array( '197157000000', '197157064000' ),
		array( '197239000000', '197239064000' )
	);
}

?>