<?php
// last updated on 4/11/15 04:11:49 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_bb extends ds_module {
	public $searchname = 'Barbados';
	public $searchlist = array(
		array( '023236000000', '023236016000' ),
		array( '065048000000', '065052000000' ),
		array( '069073192000', '069074000000' ),
		array( '199254104000', '199254112000' )
	);
}

?>