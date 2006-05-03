<?php /* $Id: dermato_rdv.php,v 1.2 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.2 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

set_time_limit( 1800 );

mbInsertCSV("modules/dPinterop/RDV.TXT", "dermato_import_rdv");

?>