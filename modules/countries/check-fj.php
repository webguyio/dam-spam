<?php
// last updated on 4/11/15 04:12:13 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_fj extends ds_module {
	public $searchname = 'Fiji';
	public $searchlist = array(
		array( '043245056000', '043245060000' ),
		array( '113020064000', '113020096000' ),
		array( '119235065000', '119235096000' ),
		array( '144120000000', '144121000000' ),
		array( '202062112000', '202062128000' ),
		array( '202151016000', '202151032000' ),
		array( '210007000000', '210007032000' )
	);
}

?>