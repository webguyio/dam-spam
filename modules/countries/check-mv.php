<?php
// last updated on 4/11/15 04:12:51 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mv extends ds_module {
	public $searchname = 'Maldives';
	public $searchlist = array(
		array( '202001195000', '202001199000' ),
		array( '202021179000', '202021180000' ),
		array( '202021181000', '202021182000' ),
		array( '202153083000', '202153084000' )
	);
}

?>