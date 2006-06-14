<?php /* $Id: do_planning_aed.php 110 2006-06-11 20:19:38Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 110 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once( $AppUI->getModuleClass("dPplanningOp", "sejour"));
require_once( $AppUI->getModuleClass("dPplanningOp", "planning"));

if ($chir_id = mbGetValueFromPost("chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}


$do = new CDoObjectAddEdit("CSejour", "sejour_id");
$do->createMsg = "Séjour créé";
$do->modifyMsg = "Séjour modifié";
$do->deleteMsg = "Séjour supprimé";
$do->doIt();

?>