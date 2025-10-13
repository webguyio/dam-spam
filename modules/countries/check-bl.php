<?php
// last updated on 4/11/15 04:11:52 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bl extends ds_module {
	public $searchname = 'Saint Barthélemy';
	public $searchlist = array(
		array( '031184224000', '031184228000' )
	);
}

?>