<?php
// last updated on 4/11/15 04:12:35 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_kn extends ds_module {
	public $searchname = 'Saint Kitts and Nevis';
	public $searchlist = array(
		array( '199021164000', '199021168000' )
	);
}

?>