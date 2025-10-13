<?php
// last updated on 4/11/15 04:12:04 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_cu extends ds_module {
	public $searchname = 'Cuba';
	public $searchlist = array(
		array( '152206064088', '152206064096' ),
		array( '190006083160', '190006083168' ),
		array( '190015150000', '190015150008' ),
		array( '200000024000', '200000028000' ),
		array( '200055153128', '200055153136' )
	);
}

?>