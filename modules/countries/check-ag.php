<?php
// last updated on 4/11/15 04:11:43 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_ag extends ds_module {
	public $searchname = 'Antigua and Barbuda';
	public $searchlist = array(
		array( '069050064000', '069050080000' ),
		array( '076076160000', '076076192000' ),
		array( '162222084000', '162222088000' ),
		array( '192064120000', '192064124000' ),
		array( '199016056000', '199016060000' ),
		array( '208083080000', '208083088000' )
	);
}

?>