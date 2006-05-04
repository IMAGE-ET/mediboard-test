<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

mbInsertCSV("modules/dPinterop/PATIENT.TXT", "import_patients", true);

mbInsertCSV("modules/dPinterop/PATIENT2.TXT", "import_patients", true);

?>