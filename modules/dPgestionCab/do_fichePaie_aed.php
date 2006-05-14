<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPgestionCab", "fichePaie"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFichePaie", "fiche_paie_id");
$do->createMsg = "Fiche créée";
$do->modifyMsg = "Fiche modifiée";
$do->deleteMsg = "Fiche supprimée";
$do->doIt();
?>