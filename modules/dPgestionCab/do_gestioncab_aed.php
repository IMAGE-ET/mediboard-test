<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPgestionCab", "gestionCab"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CGestionCab", "gestioncab_id");
$do->createMsg = "Fiche créée";
$do->modifyMsg = "Fiche modifiée";
$do->deleteMsg = "Fiche supprimée";
$do->doIt();
?>