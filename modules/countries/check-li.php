<?php
// last updated on 4/11/15 04:12:39 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_li extends ds_module {
	public $searchname = 'Liechtenstein';
	public $searchlist = array(
		array( '089248144000', '089248160000' ),
		array( '217173224000', '217173240000' )
	);
}

?>