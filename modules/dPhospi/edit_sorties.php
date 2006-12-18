<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Type d'affichage
$vue       = mbGetValueFromGetOrSession("vue"      , 0);
$typeOrder = mbGetValueFromGetOrSession("typeOrder", 1);

// Liste des services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

// Récupération de la journée à afficher
$date  = mbGetValueFromGetOrSession("date" , mbDate());

$where = array();
$ljoin = array();
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";
$ljoin["sejour"]   = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["lit"]      = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]  = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]  = "service.service_id = chambre.service_id";
$where["sortie"]   = "BETWEEN '$limit1' AND '$limit2'";
$where["type"]     = "!= 'exte'";
$where["service.group_id"] = "= '$g'";
//$where["service.service_id"] = db_prepare_in(array_keys($services));
if ($vue) {
  $where["confirme"] = "= '0'";
}
if ($typeOrder) {
  $order = "service.nom, chambre.nom, lit.nom";
} else {
  $order = "patients.nom, patients.prenom";
}

// Récupération des déplacements du jour
$deplacements = new CAffectation;
$deplacements = $deplacements->loadList($where, $order, null, null, $ljoin);
foreach($deplacements as $key => $value) {
  $deplacements[$key]->loadRefsFwd();
  
  if(!$deplacements[$key]->_ref_next->affectation_id) {
    unset($deplacements[$key]);
  } else {
    $deplacements[$key]->_ref_sejour->loadRefsFwd();
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

// Récupération des sorties ambu du jour
$where["type"] = "= 'ambu'";
$sortiesAmbu = new CAffectation;
$sortiesAmbu = $sortiesAmbu->loadList($where, $order, null, null, $ljoin);
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
  }
  $service_actuel    = $sortiesAmbu[$key]->_ref_lit->_ref_chambre->service_id;
  if(!in_array($service_actuel,array_keys($services))){
      unset($sortiesAmbu[$key]);
  }
}

// Récupération des sorties hospi complete du jour
$where["type"] = "= 'comp'";
$sortiesComp = new CAffectation;
$sortiesComp = $sortiesComp->loadList($where, $order, null, null, $ljoin);
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
  }
  $service_actuel    = $sortiesComp[$key]->_ref_lit->_ref_chambre->service_id;
  if(!in_array($service_actuel,array_keys($services))){
      unset($sortiesComp[$key]);
  }
}

// Création du template
$smarty = new CSmartyDP(1);
$smarty->assign("date"         , $date        );
$smarty->assign("deplacements" , $deplacements);
$smarty->assign("sortiesAmbu"  , $sortiesAmbu );
$smarty->assign("sortiesComp"  , $sortiesComp );
$smarty->assign("vue"          , $vue         );

$smarty->display("edit_sorties.tpl");

?>