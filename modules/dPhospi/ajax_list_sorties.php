<?php /* $Id: ajax_list_sorties.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$type      = CValue::get("type");
$vue       = CValue::getOrSession("vue"      , 0);
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_patient");

$group = CGroups::loadCurrent();


// Récupération de la liste des services
$where = array();
$where["externe"] = "= '0'";
$where["group_id"] = "= '$group->_id'";

$service  = new CService();
$services = $service->loadListWithPerms(PERM_READ, $where);

$whereDeplacement = array();
$whereSortie = array();

// Récupération de la journée à afficher
$date  = CValue::getOrSession("date" , mbDate());
$affectation  = new CAffectation;

$where = array();
$ljoin = array();
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

$ljoin["sejour"]             = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]           = "sejour.patient_id = patients.patient_id";
$ljoin["users"]              = "sejour.praticien_id = users.user_id";
$ljoin["lit"]                = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]            = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]            = "service.service_id = chambre.service_id";
$where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
$where["sejour.type"]        = "!= 'exte'";
$where["service.group_id"]   = "= '$group->_id'";
$where["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services));

if ($type == "deplacements") {
  $where["service.externe"] = "= '0'";
  $where["sejour.sortie"] = "!= affectation.sortie";
  
  if ($vue) {
    $where["effectue"] = "= '0'";
  }
  
  $orderDep = null;
  
  if($order_col == "_patient"){
    $orderDep = "patients.nom $order_way, patients.prenom, sejour.entree_prevue";
  }
  if($order_col == "_praticien"){
    $orderDep = "users.user_last_name $order_way, users.user_first_name";
  }
  if($order_col == "_chambre"){
    $orderDep = "chambre.nom $order_way, patients.nom, patients.prenom";
  }
  if($order_col == "sortie"){
    $orderDep = "affectation.sortie $order_way, patients.nom, patients.prenom";
  }
  
  // Récupération des déplacements du jour
  $deplacements = $affectation->loadList($where, $orderDep, null, null, $ljoin);
  $sejours      = CMbObject::massLoadFwdRef($deplacements, "sejour_id");
  $patients     = CMbObject::massLoadFwdRef($sejours, "patient_id");
  $praticiens   = CMbObject::massLoadFwdRef($sejours, "praticien_id");
  CMbObject::massLoadFwdRef($praticiens, "function_id");
  CMbObject::massLoadFwdRef($deplacements, "lit_id");
  
  foreach($deplacements as $_deplacement) {
    $_deplacement->loadRefsFwd();
    $sejour = $_deplacement->_ref_sejour; 
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $_deplacement->_ref_next->loadRefLit()->loadCompleteView();
  }
}
else {
  if ($vue) {
    $where["confirme"] = " = '0'";
  }
  
  $where["sejour.sortie"] = "= affectation.sortie";
  
  $order = null;

  if($order_col == "_patient"){
    $order = "patients.nom $order_way, patients.prenom, sejour.entree_prevue";
  }
  if($order_col == "_praticien"){
    $order = "users.user_last_name $order_way, users.user_first_name";
  }
  if($order_col == "sortie"){
    $order = "sejour.sortie_prevue $order_way, patients.nom, patients.prenom";
  }
  if($order_col == "_chambre"){
    $order = "chambre.nom $order_way, patients.nom, patients.prenom";
  }
  
  // Récupération des sorties du jour
  $where["sejour.type"] = " = '$type'";
  $sorties = $affectation->loadList($where, $order, null, null, $ljoin);
  
  // Chargements des détails des séjours
  foreach($sorties as $_sortie) {
    $_sortie->loadRefsFwd();
    $sejour = $_sortie->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $_sortie->_ref_next->loadRefLit(1)->loadCompleteView();
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type"         , $type);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("date"         , $date);
if ($type == "deplacements") {
  $smarty->assign("deplacements" , $deplacements);
  $smarty->assign("update_count", count($deplacements));
}
else {
  $smarty->assign("sorties"      , $sorties);
  $smarty->assign("update_count", count($sorties));
}
$smarty->assign("vue"          , $vue);
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));

$smarty->display("inc_list_sorties.tpl");

?>