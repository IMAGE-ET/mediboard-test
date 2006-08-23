<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $g;

require_once($AppUI->getModuleClass("mediusers"   , "functions" ));
require_once($AppUI->getModuleClass("dPhospi"     , "service"   ));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"  ));
require_once($AppUI->getModuleClass("dPplanningOp", "pathologie"));

global $pathos;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date       = mbGetValueFromGetOrSession("date", mbDate()); 
$heureLimit = "16:00:00";
$mode       = mbGetValueFromGetOrSession("mode");

// Nombre de patients à placer pour la semaine qui vient
$today   = mbDate()." 01:00:00";
$endWeek = mbDate("+7 days", $today);

// Initialisation de la liste des chirs, patients et plagesop
global $listChirs;
$listChirs = array();
global $listPats;
$listPats = array();

// Récupération des fonctions
global $listFunctions;
$listFunctions = new CFunctions;
$listFunctions = $listFunctions->loadList();

// Récupération du service à ajouter/éditer
$totalLits = 0;

// Récupération des chambres/services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$services = $services->loadList($where);
foreach ($services as $service_id => $service) {
  $services[$service_id]->loadRefsBack();
  $services[$service_id]->_nb_lits_dispo = 0;
  $chambres =& $services[$service_id]->_ref_chambres;
  foreach ($chambres as $chambre_id => $chambre) {
    $chambres[$chambre_id]->loadRefsBack();
    $lits =& $chambres[$chambre_id]->_ref_lits;
    foreach ($lits as $lit_id => $lit) {
      $lits[$lit_id]->loadAffectations($date);
      $affectations =& $lits[$lit_id]->_ref_affectations;
      foreach ($affectations as $affectation_id => $affectation) {
      	if(!$affectations[$affectation_id]->effectue || $mode) {
          $affectations[$affectation_id]->loadRefs();
          $affectations[$affectation_id]->checkDaysRelative($date);

          $aff_prev =& $affectations[$affectation_id]->_ref_prev;
          if ($aff_prev->affectation_id) {
            $aff_prev->loadRefsFwd();
            $aff_prev->_ref_lit->loadRefsFwd();
          }

          $aff_next =& $affectations[$affectation_id]->_ref_next;
          if ($aff_next->affectation_id) {
            $aff_next->loadRefsFwd();
            $aff_next->_ref_lit->loadRefsFwd();
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
          foreach($sejour->_ref_operations as $operation_id => $curr_operation)
            $sejour->_ref_operations[$operation_id]->loadRefCCAM();
        } else
          unset($affectations[$affectation_id]);
      }
    }

    $chambres[$chambre_id]->checkChambre();
    $services[$service_id]->_nb_lits_dispo += $chambres[$chambre_id]->_nb_lits_dispo;
    $totalLits += $chambres[$chambre_id]->_nb_lits_dispo;
  }
}

// Récupération des admissions à affecter
function loadSejourNonAffectes($where) {
  global $listChirs, $listPats, $listFunctions, $g;
  
  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
    "patients"        => "sejour.patient_id = patients.patient_id"
  );
  $where["sejour.group_id"] = "= '$g'";
  $where[] = "affectation.affectation_id IS NULL";
  $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  
  $sejourNonAffectes = new CSejour;
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, $order, null, null, $leftjoin);
  
  foreach ($sejourNonAffectes as $keySejour => $valSejour) {
    $sejour =& $sejourNonAffectes[$keySejour];
    
    // Chargement optimisé du praticien 
    if (array_key_exists($sejour->praticien_id, $listChirs)) {
      $sejour->_ref_praticien =& $listChirs[$sejour->praticien_id];
    } else {
      $sejour->loadRefPraticien();
      $sejour->_ref_praticien->_ref_function =& $listFunctions[$sejour->_ref_praticien->function_id];
      $listChirs[$sejour->praticien_id] =& $sejour->_ref_praticien;
    }
     
    // Chargement optimisé du patient
    if (array_key_exists($sejour->patient_id, $listPats)) {
      $sejour->_ref_patient =& $listPats[$sejour->patient_id];
    } else {
      $sejour->loadRefPatient();
      $listPats[$sejour->patient_id] =& $sejour->_ref_patient;
    }

    // Chargement des opérations
    $sejour->loadRefsOperations();
    foreach($sejour->_ref_operations as $keyOp => $valueOp) {
      $operation =& $sejour->_ref_operations[$keyOp];
      $operation->loadRefCCAM();
    }
  }
  
  return $sejourNonAffectes;
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDate("+7 days", $today);
$where = array(
  "entree_prevue" => "BETWEEN '$today' AND '$endWeek'",
  "type" => "!= 'exte'",
  "annule" => "= 0"
);

$alerte = loadSejourNonAffectes($where);

// Admissions de la veille
$dayBefore = mbDate("-1 days", $date);
$where = array(
  "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
  "type" => "!= 'exte'",
  "annule" => "= 0"
);

$groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where);

// Admissions du matin
$where = array(
  "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
  "type" => "!= 'exte'",
  "annule" => "= 0"
);

$groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where);

// Admissions du soir
$where = array(
  "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
  "type" => "!= 'exte'",
  "annule" => "= 0"
);

$groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where);

// Admissions antérieures
$twoDaysBefore = mbDate("-2 days", $date);
$where = array(
  "annule" => "= 0",
  "'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue"
);

$groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->debugging = false;
$smarty->assign("pathos"                 , $pathos);
$smarty->assign("date"                   , $date );
$smarty->assign("demain"                 , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"             , $heureLimit);
$smarty->assign("mode"                   , $mode);
$smarty->assign("totalLits"              , $totalLits);
$smarty->assign("services"               , $services);
$smarty->assign("alerte"                 , $alerte);
$smarty->assign("groupSejourNonAffectes" , $groupSejourNonAffectes);

$smarty->display("vw_affectations.tpl");

?>