<?php
// last updated on 4/11/15 04:12:44 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_me extends ds_module {
	public $searchname = 'Montenegro';
	public $searchlist = array(
		array( '046033192000', '046033224000' ),
		array( '109072096000', '109072112000' ),
		array( '109228064000', '109228128000' ),
		array( '213149096000', '213149128000' )
	);
}

?>