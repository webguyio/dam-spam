<?php
// last updated on 4/11/15 04:12:40 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_lr extends ds_module {
	public $searchname = 'Liberia';
	public $searchlist = array(
		array( '197231221000', '197231222000' )
	);
}

?>