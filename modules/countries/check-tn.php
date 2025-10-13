<?php
// last updated on 4/11/15 04:13:21 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_tn extends ds_module {
	public $searchname = 'Tunisia';
	public $searchlist = array(
		array( '197000000000', '197008000000' ),
		array( '213150186000', '213150188000' )
	);
}

?>