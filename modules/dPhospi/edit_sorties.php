<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Type d'affichage
$vue       = CValue::getOrSession("vue"      , 0);
$typeOrder = CValue::getOrSession("typeOrder", 1);

$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_patient");

// Liste des services
$services = new CService;
$whereDeplacement = array();
$whereSortie = array();
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

// Récupération de la journée à afficher
$date  = CValue::getOrSession("date" , mbDate());

$where = array();
$ljoin = array();
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

$ljoin["sejour"]   = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"]    = "sejour.praticien_id = users.user_id";
$ljoin["lit"]      = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]  = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]  = "service.service_id = chambre.service_id";
$where["sortie"]   = "BETWEEN '$limit1' AND '$limit2'";
$where["type"]     = "!= 'exte'";
$where["service.group_id"] = "= '$g'";
//$where["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services));

$whereDeplacement = $where;
$whereSortie = $where;

if ($vue) {
  $whereDeplacement["effectue"] = "= '0'";
  $whereSortie["confirme"] = " = '0'";
}



/*
if($order_col != "_patient_deplacement" && $order_col != "_praticien_deplacement"  && $order_col != "_chambre_deplacement"){
  $order_col = "_patient";	
}
*/
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
$deplacements = new CAffectation;
$deplacements = $deplacements->loadList($whereDeplacement, $orderDep, null, null, $ljoin);
foreach($deplacements as $key => $value) {
  $deplacements[$key]->loadRefsFwd();
    
  if(!$deplacements[$key]->_ref_next->affectation_id) {
    unset($deplacements[$key]);
  } else {
    $deplacements[$key]->_ref_sejour->loadRefsFwd(1);
    $deplacements[$key]->_ref_sejour->_ref_praticien->loadRefsFwd();
    $deplacements[$key]->_ref_lit->loadCompleteView();
    $deplacements[$key]->_ref_lit->loadRefChambre();
    $deplacements[$key]->_ref_next->loadRefsFwd();
    $deplacements[$key]->_ref_next->_ref_lit->loadCompleteView();
    $deplacements[$key]->_ref_next->_ref_lit->loadRefChambre();
    
    $service_actuel    = $deplacements[$key]->_ref_lit->_ref_chambre->service_id;
    $service_transfert = $deplacements[$key]->_ref_next->_ref_lit->_ref_chambre->service_id;
    
    if(!in_array($service_actuel,array_keys($services)) && !in_array($service_transfert,array_keys($services))){
      unset($deplacements[$key]);
    }
  }
}


foreach($deplacements as $key => $deplacement){ 
  for($i = -10; $i < 10; $i++) {
    $timing[$deplacement->_id][] = mbDateTime("$i minutes", $deplacement->sortie);
  }
} 


/*
if($order_col != "_patient" && $order_col != "_praticien" && $order_col != "sortie" && $order_col != "_chambre"){
  $order_col = "_patient";	
}
*/
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


// Récupération des sorties ambu du jour
$whereSortie["type"] = "= 'ambu'";
$sortiesAmbu = new CAffectation;
$sortiesAmbu = $sortiesAmbu->loadList($whereSortie, $order, null, null, $ljoin);
foreach($sortiesAmbu as $key => $value) {
  $sortiesAmbu[$key]->loadRefsFwd();
  if($sortiesAmbu[$key]->_ref_next->affectation_id) {
    unset($sortiesAmbu[$key]);
  } else {
    $sortiesAmbu[$key]->_ref_sejour->loadRefsFwd();
    $sortiesAmbu[$key]->_ref_sejour->_ref_praticien->loadRefsFwd();
    $sortiesAmbu[$key]->_ref_lit->loadCompleteView();
    $sortiesAmbu[$key]->_ref_next->loadRefsFwd();
    $sortiesAmbu[$key]->_ref_next->_ref_lit->loadCompleteView();
    
    $service_actuel    = $sortiesAmbu[$key]->_ref_lit->_ref_chambre->service_id;
    if(!in_array($service_actuel,array_keys($services))){
        unset($sortiesAmbu[$key]);
    }
  }
}

// Récupération des sorties hospi complete du jour
$whereSortie["type"] = "= 'comp'";
$sortiesComp = new CAffectation;
$sortiesComp = $sortiesComp->loadList($whereSortie, $order, null, null, $ljoin);
foreach($sortiesComp as $key => $value) {
  $sortiesComp[$key]->loadRefsFwd();
  if($sortiesComp[$key]->_ref_next->affectation_id) {
    unset($sortiesComp[$key]);
  } else {
    $sortiesComp[$key]->_ref_sejour->loadRefsFwd();
    $sortiesComp[$key]->_ref_sejour->_ref_praticien->loadRefsFwd();
    $sortiesComp[$key]->_ref_lit->loadCompleteView();
    $sortiesComp[$key]->_ref_next->loadRefsFwd();
    $sortiesComp[$key]->_ref_next->_ref_lit->loadCompleteView();
    
    $service_actuel    = $sortiesComp[$key]->_ref_lit->_ref_chambre->service_id;
    if(!in_array($service_actuel,array_keys($services))){
        unset($sortiesComp[$key]);
    }
  }
}

  
// Création du template
$smarty = new CSmartyDP();

if($deplacements){
  $smarty->assign("timing"       , $timing      );
}
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("date"         , $date        );
$smarty->assign("deplacements" , $deplacements);
$smarty->assign("sortiesAmbu"  , $sortiesAmbu );
$smarty->assign("sortiesComp"  , $sortiesComp );
$smarty->assign("vue"          , $vue         );
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));

$smarty->display("edit_sorties.tpl");

?>