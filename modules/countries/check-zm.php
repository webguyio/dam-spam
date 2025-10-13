<?php
// last updated on 4/11/15 04:13:33 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_zm extends ds_module {
	public $searchname = 'Zambia';
	public $searchlist = array(
		array( '197212000000', '197213000000' ),
		array( '197220012000', '197220014000' ),
		array( '197220192000', '197221000000' )
	);
}

?>