<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$transmission_id  = CValue::get("transmission_id");
$data_id          = CValue::get("data_id");
$action_id        = CValue::get("action_id");
$result_id        = CValue::get("result_id");
$sejour_id        = CValue::get("sejour_id");
$object_id        = CValue::get("object_id");
$object_class     = CValue::get("object_class");
$libelle_ATC      = CValue::get("libelle_ATC");
$refreshTrans     = CValue::get("refreshTrans", 0);
$update_plan_soin = CValue::get("update_plan_soin", 0);
$user_id          = CUser::get()->_id;

$transmission =  new CTransmissionMedicale;
if ($transmission_id) {
  $transmission->load($transmission_id);
}
else if ($data_id || $action_id || $result_id){
  $transmission->sejour_id = $sejour_id;

  // Multi-transmissions
  if ($data_id) {
    $trans = new CTransmissionMedicale;
    $trans->load($data_id);
    $trans->canEdit();
    $transmission->_text_data = $trans->text;
    $transmission->user_id    = $trans->user_id;
    $transmission->date       = $trans->date;
    $transmission->degre      = $trans->degre;
    if ($trans->object_id && $trans->object_class) {
      $transmission->object_id = $trans->object_id;
      $transmission->object_class = $trans->object_class;
    }
    else if ($trans->libelle_ATC) {
      $transmission->libelle_ATC = stripslashes($trans->libelle_ATC);
    }
  }
  if ($action_id) {
    $trans = new CTransmissionMedicale;
    $trans->load($action_id);
    $trans->canEdit();
    $transmission->_text_action = $trans->text;
    $transmission->user_id      = $trans->user_id;
    $transmission->date         = $trans->date;
    $transmission->degre        = $trans->degre;
    if ($trans->object_id && $trans->object_class) {
      $transmission->object_id = $trans->object_id;
      $transmission->object_class = $trans->object_class;
    }
    else if ($trans->libelle_ATC) {
      $transmission->libelle_ATC = stripslashes($trans->libelle_ATC);
    }
  }
  if ($result_id) {
    $trans = new CTransmissionMedicale;
    $trans->load($result_id);
    $trans->canEdit();
    $transmission->_text_result = $trans->text;
    $transmission->user_id      = $trans->user_id;
    $transmission->date         = $trans->date;
    $transmission->degre        = $trans->degre;
    if ($trans->object_id && $trans->object_class) {
      $transmission->object_id = $trans->object_id;
      $transmission->object_class = $trans->object_class;
    }
    else if ($trans->libelle_ATC) {
      $transmission->libelle_ATC = stripslashes($trans->libelle_ATC);
    }
  }
}
else {
  $transmission->sejour_id = $sejour_id;
  $transmission->user_id = $user_id;
  if ($object_id && $object_class) {
    $transmission->object_id = $object_id;
    $transmission->object_class = $object_class;
  }
  else if ($libelle_ATC) {
    $transmission->libelle_ATC = stripslashes($libelle_ATC);
  }
}

$transmission->loadTargetObject();

if ($transmission->object_class == "CAdministration") {
  $transmission->_ref_object->loadRefsFwd();
}

$smarty = new CSmartyDP;

$smarty->assign("transmission", $transmission);
$smarty->assign("refreshTrans", $refreshTrans);
$smarty->assign("update_plan_soin", $update_plan_soin);
$smarty->assign("data_id"  , $data_id);
$smarty->assign("action_id", $action_id);
$smarty->assign("result_id", $result_id);
$smarty->assign("date", CMbDT::date());
$smarty->assign("hour", CMbDT::transform(null, CMbDT::time(), "%H"));

$smarty->display("inc_transmission.tpl");
?>