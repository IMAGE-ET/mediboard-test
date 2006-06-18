<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if($chir_id = dPgetParam( $_POST, 'chir_id', null))
  mbSetValueToSession('chir_id', $chir_id);


$do = new CDoObjectAddEdit("COperation", "operation_id");
$do->doBind();

if (intval(dPgetParam($_POST, 'del'))) {
  mbSetValueToSession("operation_id");
  $do->deleteMsg = "Opération supprimée";
  $do->redirectDelete = "m=$m&tab=vw_edit_planning";
} else {
  $do->modifyMsg = "Opération modifiée";
  $do->createMsg = "Opération créée";
  $do->doStore();
  if($otherm = dPgetParam( $_POST, 'otherm', 0)) {
    $m = $otherm;
  }
  if($m == "dPhospi") {
    $do->redirectStore = "m=$m#operation".$do->_obj->operation_id;
  }
  $do->redirectStore = "m=$m&operation_id=".$do->_obj->operation_id;
}
$do->doRedirect();

?>