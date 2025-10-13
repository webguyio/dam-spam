<?php
// last updated on 4/11/15 04:13:09 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_sd extends ds_module {
	public $searchname = 'Sudan';
	public $searchlist = array(
		array( '197251068000', '197251072000' ),
		array( '197252000000', '197252016000' ),
		array( '197254192000', '197254208000' ),
		array( '197254240000', '197255000000' ),
		array( '212000128000', '212000135000' ),
		array( '212000139032', '212000140000' )
	);
}

?>