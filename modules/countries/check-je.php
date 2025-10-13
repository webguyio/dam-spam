<?php
// last updated on 4/11/15 04:12:31 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_je extends ds_module {
	public $searchname = 'Jersey';
	public $searchlist = array(
		array( '081020177000', '081020178000' ),
		array( '081020185000', '081020186000' )
	);
}

?>