<?php
// last updated on 4/11/15 04:12:31 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_jm extends ds_module {
	public $searchname = 'Jamaica';
	public $searchlist = array(
		array( '072027000000', '072027128000' ),
		array( '072027192000', '072027224000' ),
		array( '074116056000', '074116060000' ),
		array( '184170000000', '184170064000' )
	);
}

?>