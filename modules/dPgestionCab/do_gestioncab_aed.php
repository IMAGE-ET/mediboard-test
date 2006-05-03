<?php /* $Id: do_gestioncab_aed.php,v 1.1 2006/04/05 00:02:41 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: 1.1 $
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