<?php
// last updated on 4/11/15 04:12:18 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_gh extends ds_module {
	public $searchname = 'Ghana';
	public $searchlist = array(
		array( '197251128000', '197251224000' )
	);
}

?>