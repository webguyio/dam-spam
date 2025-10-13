<?php
// last updated on 4/11/15 04:13:29 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_vi extends ds_module {
	public $searchname = 'U.S. Virgin Islands';
	public $searchlist = array(
		array( '208084192000', '208084200000' )
	);
}

?>