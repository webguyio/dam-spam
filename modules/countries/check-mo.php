<?php
// last updated on 4/11/15 04:12:47 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mo extends ds_module {
	public $searchname = 'Macao';
	public $searchlist = array(
		array( '027109128000', '027109160000' ),
		array( '027109224000', '027110000000' ),
		array( '060246224000', '060247000000' ),
		array( '113052089000', '113052090000' ),
		array( '202086185000', '202086186000' ),
		array( '202171252000', '202172000000' ),
		array( '202175083000', '202175084000' ),
		array( '202175122000', '202175123000' )
	);
}

?>