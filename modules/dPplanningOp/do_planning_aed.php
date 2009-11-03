<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $m;

$do = new CDoObjectAddEdit("COperation", "operation_id");
$do->doBind();

if(intval(CValue::post("del", null))) {
  CValue::setSession("operation_id");
  $do->redirectDelete = "m=$m&tab=vw_edit_planning&operation_id=0";
  $do->doDelete();
} else {
  if($do->_obj->plageop_id && ($do->_objBefore->plageop_id != $do->_obj->plageop_id)) {
    $do->_obj->rank = 0;
  }
  $do->doStore();
  if($do->_obj->plageop_id && $do->_objBefore->plageop_id && ($do->_objBefore->plageop_id != $do->_obj->plageop_id)) {
    $plageop = new CPlageOp;
    $plageop->load($do->_objBefore->plageop_id);
    $plageop->spec_id = "";
    $plageop->store();
  }
  $m = CValue::post("otherm", $m);
  if($m == "dPhospi") {
    $do->redirectStore = "m=$m#operation".$do->_obj->operation_id;
  }
  $do->redirectStore = "m=$m&operation_id=".$do->_obj->operation_id;
}
$do->doRedirect();

?>