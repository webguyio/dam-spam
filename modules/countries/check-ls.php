<?php
// last updated on 4/11/15 04:12:41 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ls extends ds_module {
	public $searchname = 'Lesotho';
	public $searchlist = array(
		array( '197155194000', '197155195000' )
	);
}

?>