<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");


$service_id = mbGetValueFromGetOrSession("service_id" , null);
$date       = mbGetValueFromGetOrSession("date"       , mbDate());

$service = null;

// Liste des services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);
foreach($services as &$service){
  $service->validationRepas($date);
}

$listTypeRepas = new CTypeRepas;
$order = "debut, fin, nom";
$listTypeRepas = $listTypeRepas->loadList(null,$order);
      
if(!$service_id || !array_key_exists($service_id,$services)){
  mbSetValueToSession("service_id", null);
  $service_id = null ;
}else{
  $service =& $services[$service_id].
  $service->loadRefsBack();
  
  foreach ($service->_ref_chambres as $chambre_id => &$chambre) {
    $chambre->loadRefsBack();
    foreach ($chambre->_ref_lits as $lit_id => &$lit) {
      $lit->loadAffectations($date);
      foreach ($lit->_ref_affectations as $affectation_id => &$affectation) {
        $affectation->loadRefSejour();
        //$affectation->loadRefsAffectations();
        
        if(!$affectation->_ref_sejour->sejour_id || $affectation->_ref_sejour->type == "ambu"){
          unset($lit->_ref_affectations[$affectation_id]);
        }else{
          $affectation->_ref_sejour->_ref_patient =& getCachedPatient($affectation->_ref_sejour->patient_id);
          $affectation->loadMenu($date,$listTypeRepas);
        }
      }
      if(!count($lit->_ref_affectations)){
        unset($chambre->_ref_lits[$lit_id]);
      }
    }
    if(!count($chambre->_ref_lits)){
      unset($service->_ref_chambres[$chambre_id]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"          , $date);
$smarty->assign("listTypeRepas" , $listTypeRepas);
$smarty->assign("service_id"    , $service_id);
$smarty->assign("services"      , $services);
$smarty->assign("service"       , $service);

$smarty->display("vw_planning_repas.tpl");
?>