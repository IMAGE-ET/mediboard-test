<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$group = CGroups::loadCurrent();

// Type d'affichage
$vue        = CValue::getOrSession("vue"      , 0);
$typeOrder  = CValue::getOrSession("typeOrder", 1);
$service_id = CValue::getOrSession("service_id");

$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_patient");

// Récupération de la liste des services
$where = array();
$where["externe"] = "= '0'";
$where["group_id"] = "= '$group->_id'";

$service = new CService();
$service->load($service_id);
$services = $service->loadListWithPerms(PERM_READ, $where);

$whereDeplacement = array();
$whereSortie = array();

// Récupération de la journée à afficher
$date  = CValue::getOrSession("date" , mbDate());

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

$whereSortie      = $where;
$whereDeplacement = $where;

$whereDeplacement["service.externe"]    = "= '0'";

if ($vue) {
  $whereDeplacement["effectue"] = "= '0'";
  $whereSortie["confirme"] = " = '0'";
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
$affectation  = new CAffectation;
$deplacements = $affectation->loadList($whereDeplacement, $orderDep, null, null, $ljoin);
$sejours      = CMbObject::massLoadFwdRef($deplacements, "sejour_id");
$patients     = CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens   = CMbObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");
CMbObject::massLoadFwdRef($deplacements, "lit_id");

foreach($deplacements as $_deplacement) {
  $_deplacement->loadRefsFwd();
  
  if(!$_deplacement->_ref_next->_id) {
    unset($deplacements[$_deplacement->_id]);
    continue;
  }
  $sejour = $_deplacement->_ref_sejour; 
  $sejour->loadRefPatient(1);
  $sejour->loadRefPraticien(1);
  $_deplacement->_ref_next->loadRefLit()->loadCompleteView();
  
  $service_actuel    = $_deplacement->_ref_lit->_ref_chambre->service_id;
  $service_transfert = $_deplacement->_ref_next->_ref_lit->_ref_chambre->service_id;
  $show_deplacement = array_key_exists($service_actuel, $services) || array_key_exists($service_transfert, $services);
  if($service->_id){
    $show_deplacement &= ($service_actuel == $service->_id) || ($service_transfert == $service->_id);
  }
  if (!$show_deplacement){
    unset($deplacements[$_deplacement->_id]);
  }
}

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

// Récupération des sorties ambu et hospi du jour
$sorties = array();

$whereSortie["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services), $service->_id);

$whereSortie["type"] = "= 'comp'";
$sorties["comp"]     = $affectation->loadList($whereSortie, $order, null, null, $ljoin);

$whereSortie["type"] = "= 'ambu'";
$sorties["ambu"]     = $affectation->loadList($whereSortie, $order, null, null, $ljoin);

// Chargements des détails des séjours
foreach($sorties as &$_sorties) {
  foreach($_sorties as $_sortie) {
    $_sortie->loadRefsFwd();
    if($_sortie->_ref_next->_id) {
      unset($_sorties[$_sortie->_id]);
      continue;
    }
		
    $sejour = $_sortie->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $_sortie->loadRefLit(1)->loadCompleteView();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("date"         , $date);
$smarty->assign("services"     , $services);
$smarty->assign("service"      , $service);
$smarty->assign("deplacements" , $deplacements);
$smarty->assign("sorties"      , $sorties);
$smarty->assign("vue"          , $vue);
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));

$smarty->display("edit_sorties.tpl");

?>