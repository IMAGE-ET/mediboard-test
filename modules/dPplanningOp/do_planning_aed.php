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
$listTimes = array("entree_bloc", "pose_garrot", "debut_op",
                   "sortie_bloc", "retrait_garrot", "fin_op",
                   "entree_reveil", "sortie_reveil", "induction");
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
  $do->redirectDelete = "m=$m&tab=vw_edit_planning";
  $do->doDelete();
} else {
  if($do->_obj->annulee) {
    $do->_obj->rank = 0;
  }
  $do->modifyMsg = "Opération modifiée";
  $do->createMsg = "Opération créée";
  $do->doStore();
  $m = mbGetValueFromPost("otherm", $m);
  if($m == "dPhospi") {
    $do->redirectStore = "m=$m#operation".$do->_obj->operation_id;
  }
  $do->redirectStore = "m=$m&operation_id=".$do->_obj->operation_id;
}
$do->doRedirect();

?>