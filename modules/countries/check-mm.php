<?php
// last updated on 4/11/15 04:12:47 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_mm extends ds_module {
	public $searchname = 'Myanmar [Burma]';
	public $searchlist = array(
		array( '103025012000', '103025016000' ),
		array( '103255172000', '103255176000' ),
		array( '122248112000', '122248128000' ),
		array( '203081064000', '203081096000' ),
		array( '203081162000', '203081163000' )
	);
}

?>