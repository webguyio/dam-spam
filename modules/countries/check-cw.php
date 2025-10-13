<?php
// last updated on 4/11/15 04:12:05 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_cw extends ds_module {
	public $searchname = 'Curacao';
	public $searchlist = array(
		array( '190088128000', '190089000000' ),
		array( '190112224000', '190112240000' ),
		array( '190185000000', '190185080000' )
	);
}

?>