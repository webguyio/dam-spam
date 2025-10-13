<?php
// last updated on 4/11/15 04:11:57 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bw extends ds_module {
	public $searchname = 'Botswana';
	public $searchlist = array(
		array( '083143024000', '083143032000' ),
		array( '168167000000', '168168000000' )
	);
}

?>