<?php
// last updated on 4/11/15 04:13:22 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_tt extends ds_module {
	public $searchname = 'Trinidad and Tobago';
	public $searchlist = array(
		array( '064028128000', '064028144000' ),
		array( '131100036000', '131100040000' ),
		array( '161000224000', '161001000000' ),
		array( '181188000000', '181188128000' ),
		array( '186044000000', '186046000000' ),
		array( '190006224000', '190006240000' ),
		array( '190058000000', '190060000000' ),
		array( '190083128000', '190084000000' ),
		array( '190213000000', '190214000000' ),
		array( '200007088000', '200007096000' ),
		array( '209094192000', '209094224000' )
	);
}

?>