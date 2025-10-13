<?php
// last updated on 4/11/15 04:12:37 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ky extends ds_module {
	public $searchname = 'Cayman Islands';
	public $searchlist = array(
		array( '074117216000', '074117224000' ),
		array( '162249128000', '162249136000' ),
		array( '199201084000', '199201088000' )
	);
}

?>