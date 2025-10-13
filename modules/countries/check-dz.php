<?php
// last updated on 4/11/15 04:12:09 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_dz extends ds_module {
	public $searchname = 'Algeria';
	public $searchlist = array(
		array( '105096000000', '105112000000' ),
		array( '193194064000', '193194080000' ),
		array( '193194082160', '193194082192' ),
		array( '197119000000', '197120000000' ),
		array( '197200000000', '197200128000' ),
		array( '197203000000', '197204000000' ),
		array( '197205000000', '197206000000' ),
		array( '213140032000', '213140064000' )
	);
}

?>