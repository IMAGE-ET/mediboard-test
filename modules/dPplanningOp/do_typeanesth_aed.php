<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPPlanningOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPplanningOp", "typeanesth"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CTypeAnesth", "type_anesth_id");
$do->createMsg = "Type d'anesthésie créé";
$do->modifyMsg = "Type d'anesthésie modifié";
$do->deleteMsg = "Type d'anesthésie supprimé";
$do->doIt();

?>