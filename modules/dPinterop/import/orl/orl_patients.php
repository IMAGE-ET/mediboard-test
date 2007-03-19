<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

mbInsertCSV("modules/dPinterop/PATIENT.TXT", "import_patients", true);

mbInsertCSV("modules/dPinterop/PATIENT2.TXT", "import_patients", true);

?>