<?php
// last updated on 4/11/15 04:12:01 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ck extends ds_module {
	public $searchname = 'Cook Islands';
	public $searchlist = array(
		array( '202065032000', '202065064000' )
	);
}

?>