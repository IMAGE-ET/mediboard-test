<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$chir_id = mbGetValueFromPostOrSession("chir_id", null);

// lignes pour rentrer l'heure courante du serveur dans certains champs
$listTimes = array("entree_bloc", "entree_salle", "pose_garrot", "debut_op",
                   "sortie_salle", "retrait_garrot", "fin_op",
                   "entree_reveil", "sortie_reveil", "induction_debut", "induction_fin");
foreach($listTimes as $curr_item) {
  if(isset($_POST[$curr_item])) {
    if($_POST[$curr_item] == "current") {
      $_POST[$curr_item] = mbTranformTime(null, null, "%H:%M:00");
    }
  }
}

$do = new CDoObjectAddEdit("COperation", "operation_id");
$do->doBind();

if(intval(mbGetValueFromPost("del", null))) {
  mbSetValueToSession("operation_id");
  $do->deleteMsg = "Opération supprimée";
  $do->redirectDelete = "m=$m&tab=vw_edit_planning&operation_id=0";
  $do->doDelete();
} else {
  $do->modifyMsg = "Opération modifiée";
  $do->createMsg = "Opération créée";
  if($do->_obj->plageop_id && ($do->_objBefore->plageop_id != $do->_obj->plageop_id)) {
    $do->_obj->rank = 0;
  }
  $do->doStore();
  if($do->_obj->plageop_id && $do->_objBefore->plageop_id && ($do->_objBefore->plageop_id != $do->_obj->plageop_id)) {
    $plageop = new CPlageOp;
    $plageop->load($do->_objBefore->plageop_id);
    $plageop->store();
  }
  $m = mbGetValueFromPost("otherm", $m);
  if($m == "dPhospi") {
    $do->redirectStore = "m=$m#operation".$do->_obj->operation_id;
  }
  $do->redirectStore = "m=$m&operation_id=".$do->_obj->operation_id;
}
$do->doRedirect();

?>