<?php /* $Id: do_affectation_aed.php,v 1.3 2005/05/04 16:57:45 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.3 $
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass($m, "affectation"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CAffectation", "affectation_id");
$do->createMsg = "Affectation créée";
$do->modifyMsg = "Affectation modifiée";
$do->deleteMsg = "Affectation supprimée";
$do->doIt();
?>