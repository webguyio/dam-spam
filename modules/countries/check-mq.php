<?php
// last updated on 4/11/15 04:12:48 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mq extends ds_module {
	public $searchname = 'Martinique';
	public $searchlist = array(
		array( '095138000000', '095138128000' )
	);
}

?>