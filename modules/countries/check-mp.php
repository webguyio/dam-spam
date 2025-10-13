<?php
// last updated on 4/11/15 04:12:48 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mp extends ds_module {
	public $searchname = 'Northern Mariana Islands';
	public $searchlist = array(
		array( '210023080000', '210023096000' )
	);
}

?>