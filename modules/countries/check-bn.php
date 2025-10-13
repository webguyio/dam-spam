<?php
// last updated on 4/11/15 04:11:54 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bn extends ds_module {
	public $searchname = 'Brunei';
	public $searchlist = array(
		array( '119160144000', '119160148000' ),
		array( '119160171000', '119160172000' ),
		array( '119160176000', '119160184000' ),
		array( '119160188000', '119160192000' ),
		array( '202160016000', '202160020000' ),
		array( '202160034000', '202160036000' )
	);
}

?>