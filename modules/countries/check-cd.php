<?php
// last updated on 4/11/15 04:11:59 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_cd extends ds_module {
	public $searchname = 'Democratic Republic of the Congo';
	public $searchlist = array(
		array( '083229064000', '083229128000' ),
		array( '193110104000', '193110106000' ),
		array( '217171084000', '217171085000' ),
		array( '217171087000', '217171088000' )
	);
}

?>