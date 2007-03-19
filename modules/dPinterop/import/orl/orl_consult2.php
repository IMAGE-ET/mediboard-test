<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

mbInsertCSV("modules/dPinterop/CONSULTATIONS.TXT", "import_consultations2");

?>