<?php /* $Id: vw_affectations.php 1059 2006-10-09 08:28:41Z maskas $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1059 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $g;

global $pathos;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date       = mbGetValueFromGetOrSession("date", mbDate()); 
$heureLimit = "16:00:00";
$mode       = mbGetValueFromGetOrSession("mode", 0);

// Initialisation de la liste des chirs, patients et plagesop
global $listChirs;
$listChirs = array();
global $listPats;
$listPats = array();
global $listLits;
$listLists = array();

// Récupération des fonctions
global $listFunctions;
$listFunctions = new CFunctions;
$listFunctions = $listFunctions->loadList();

// Récupération du service
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$services = $services->loadList($where);
$service_id = mbGetValueFromGet("service_id");
$service =& $services[$service_id];

// Affichage ou non des services
$vwService = array();
$vwServiceCookie = mbGetValueFromCookie("fullService", null);
foreach ($services as $curr_service_id => $curr_service) {
  $vwService[$curr_service_id] = 1;
}
if($vwServiceCookie) {
  $vwServiceCookieArray = explode("@", $vwServiceCookie);
  mbRemoveValuesInArray("", $vwServiceCookieArray);
  foreach($vwServiceCookieArray as $element) {
    $matches = null;
    preg_match("/service(\d+)-trigger:trigger(Show|Hide)/i", $element, $matches);
    if($matches[2] == "Show") {
      $vwService[$matches[1]] = 0;
    }
  }
}


if($vwService[$service_id]) {
  $service->_vwService = 1;
  $service->loadRefsBack();
  $service->_nb_lits_dispo = 0;
  $chambres =& $service->_ref_chambres;
  foreach ($chambres as $chambre_id => $chambre) {
    $chambres[$chambre_id]->loadRefsBack();
    $lits =& $chambres[$chambre_id]->_ref_lits;
    foreach ($lits as $lit_id => $lit) {
      $lits[$lit_id]->loadAffectations($date);
      $affectations =& $lits[$lit_id]->_ref_affectations;
      foreach ($affectations as $affectation_id => $affectation) {
        if(!$affectations[$affectation_id]->effectue || $mode) {
          $affectations[$affectation_id]->loadRefSejour();
          $affectations[$affectation_id]->loadRefsAffectations();
          $affectations[$affectation_id]->checkDaysRelative($date);

          $aff_prev =& $affectations[$affectation_id]->_ref_prev;
          if ($aff_prev->affectation_id) {
            if(isset($listLits[$aff_prev->lit_id])) {
              $aff_prev->_ref_lit =& $listLits[$aff_prev->lit_id];
            } else {
              $aff_prev->loadRefLit();
              $aff_prev->_ref_lit->loadRefChambre();
              $listLits[$aff_prev->lit_id] =& $aff_prev->_ref_lit;
            }
          }

          $aff_next =& $affectations[$affectation_id]->_ref_next;
          if ($aff_next->affectation_id) {
            if(isset($listLits[$aff_next->lit_id])) {
              $aff_prev->_ref_lit =& $listLits[$aff_next->lit_id];
            } else {
              $aff_next->loadRefLit();
              $aff_next->_ref_lit->loadRefChambre();
              $listLits[$aff_next->lit_id] =& $aff_next->_ref_lit;
            }
          }

          $sejour =& $affectations[$affectation_id]->_ref_sejour;
          $sejour->loadRefsOperations();
          if(isset($listChirs[$sejour->praticien_id])) {
            $sejour->_ref_praticien =& $listChirs[$sejour->praticien_id];
          }
          else {
            $sejour->loadRefPraticien();
            $sejour->_ref_praticien->_ref_function =& $listFunctions[$sejour->_ref_praticien->function_id];
            $listChirs[$sejour->praticien_id] =& $sejour->_ref_praticien;
          }
          if(isset($listPats[$sejour->patient_id])) {
            $sejour->_ref_patient =& $listPats[$sejour->patient_id];
          }
          else {
            $sejour->loadRefPatient();
            $listPats[$sejour->patient_id] =& $sejour->_ref_patient;
          }
          foreach($sejour->_ref_operations as $operation_id => $curr_operation) {
            $sejour->_ref_operations[$operation_id]->loadRefCCAM();
          }
          $affectations[$affectation_id]->_ref_sejour->_ref_patient->verifCmuEtat($affectations[$affectation_id]->_ref_sejour->_date_entree_prevue);
        } else {
          unset($affectations[$affectation_id]);
        }
      }
    }
    $chambres[$chambre_id]->checkChambre();
  }
} else {
  $service->_vwService = 0;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("date"        , $date );
$smarty->assign("demain"      , mbDate("+ 1 day", $date));
$smarty->assign("vwService"   , $vwService);
$smarty->assign("curr_service", $service);

$smarty->display("inc_affectations_services.tpl");

?>