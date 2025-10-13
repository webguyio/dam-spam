<?php
// last updated on 4/11/15 04:13:25 PM

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_uk extends ds_module {
	public $searchname = 'United Kingdom';
	public $searchlist = array(
		array( '190124251192', '190124252000' ),
		array( '191101052000', '191101053000' ),
		array( '200055243192', '200055244000' )
	);
}

?>