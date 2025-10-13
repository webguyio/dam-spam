<?php
// last updated on 4/11/15 04:11:47 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_aw extends ds_module {
	public $searchname = 'Aruba';
	public $searchlist = array(
		array( '201229000000', '201229128000' )
	);
}

?>