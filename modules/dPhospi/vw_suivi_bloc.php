<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPospi
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkRead();

$group = CGroups::loadCurrent();

$ds = CSQLDataSource::get("std");
$service_id = CValue::getOrSession("service_id", 0);
$bloc_id    = CValue::getOrSession("bloc_id"   , 0);
$date_suivi = CValue::getOrSession("date_suivi", mbDate());
$listOps = array();

// Liste des services
$service = new CService();
$where = array();
$where["group_id"]  = "= '$group->_id'";
$where["cancelled"] = "= '0'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ,$where, $order);

// Liste des blocs
$bloc = new CBlocOperatoire();
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";
$blocs = $bloc->loadListWithPerms(PERM_READ,$where, $order);

// Listes des interventions
$operation = new COperation();
$ljoin = array(
  "plagesop"   => "`operations`.`plageop_id` = `plagesop`.`plageop_id`",
  "sallesbloc" => "`operations`.`salle_id` = `sallesbloc`.`salle_id`",
  "sejour"     => "`operations`.`sejour_id` = `sejour`.`sejour_id`");
$where = array();
$where[] = "`plagesop`.`date` = '$date_suivi' OR `operations`.`date` = '$date_suivi'";
if($bloc_id) {
  $where["sallesbloc.bloc_id"] = "= '$bloc_id'";
}
$where["operations.annulee"] = "= '0'";
$where["sejour.group_id"] = "= '$group->_id'";
$order = "operations.time_operation";
$listOps = $operation->loadList($where,$order, null, null, $ljoin);

$listServices = array();
// Chargement des infos des interventions
foreach($listOps as $_key => $_op) {
  $_op->loadRefChir();
  $_op->_ref_chir->loadRefFunction();
  $_op->loadRefSejour();
  $_op->_ref_sejour->loadRefPatient();
  $_op->loadRefAffectation();
  $_op->loadExtCodesCCAM();
  if (!in_array($_op->_ref_affectation->_ref_lit->_ref_chambre->service_id, array_keys($services))) {
    unset($listOps[$_key]);
    continue;
  }
  if($_op->_ref_affectation->_id) {
    if(!$service_id || $service_id == $_op->_ref_affectation->_ref_lit->_ref_chambre->service_id) {
      $listServices[$_op->_ref_affectation->_ref_lit->_ref_chambre->service_id][$_op->_id] = $_op;
    }
  } elseif(!$service_id) {
    $listServices["NP"][$_op->_id] = $_op;
  }
} 


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_suivi"  , $date_suivi);
$smarty->assign("listServices", $listServices);
$smarty->assign("services"    , $services);
$smarty->assign("service_id"  , $service_id);
$smarty->assign("blocs"       , $blocs);
$smarty->assign("bloc_id"     , $bloc_id);

$smarty->display("vw_suivi_bloc.tpl");
?>