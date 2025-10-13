<?php
// last updated on 4/11/15 04:11:51 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bf extends ds_module {
	public $searchname = 'Burkina Faso';
	public $searchlist = array(
		array( '212052137000', '212052137064' )
	);
}

?>