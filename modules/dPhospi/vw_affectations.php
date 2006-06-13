<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("dPhospi", "service"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPplanningOp", "pathologie"));

global $pathos;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$heureLimit = "16:00:00";
$mode = mbGetValueFromGetOrSession("mode");

// Initialisation de la liste des chirs, patients et plagesop
$listChirs = array();
$listPats = array();

// Récupération des fonctions
$listFunctions = new CFunctions;
$listFunctions = $listFunctions->loadList();

// Récupération du service à ajouter/éditer
//$serviceSel = new CService;
//$serviceSel->load(mbGetValueFromGetOrSession("service_id"));
$totalLits = 0;

// Récupération des chambres/services
$services = new CService;
$services = $services->loadList();
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
          foreach($sejour->_ref_operations as $operation_id => $operation)
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
$leftjoin = array(
  "affectation"     => "sejour.sejour_id = affectation.sejour_id",
  "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  "patients" => "sejour.patient_id = patients.patient_id"
);
$ljwhere = "affectation.affectation_id IS NULL";
$order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";

// Admissions de la veille
$where = array(
  "entree_prevue" => "BETWEEN '".mbDate("-1 days", $date)." 00:00:00' AND '$date 00:00:00'",
  "type" => "!= 'exte'",
  "annule" => "= 0",
  $ljwhere  
);
$sejourNonAffectesVeille = new CSejour;
$sejourNonAffectesVeille = $sejourNonAffectesVeille->loadList($where, $order, null, null, $leftjoin);

foreach ($sejourNonAffectesVeille as $sejour_id => $sejour) {
   if(isset($listChirs[$sejourNonAffectesVeille[$sejour_id]->praticien_id])) {
     $sejourNonAffectesVeille[$sejour_id]->_ref_praticien =& $listChirs[$sejourNonAffectesVeille[$sejour_id]->praticien_id];
   }
   else {
     $sejourNonAffectesVeille[$sejour_id]->loadRefPraticien();
     $sejourNonAffectesVeille[$sejour_id]->_ref_praticien->_ref_function =& $listFunctions[$sejourNonAffectesVeille[$sejour_id]->_ref_praticien->function_id];
     $listChirs[$sejourNonAffectesVeille[$sejour_id]->praticien_id] =& $sejourNonAffectesVeille[$sejour_id]->_ref_praticien;
   }
   if(isset($listPats[$sejourNonAffectesVeille[$sejour_id]->patient_id])) {
     $sejorNonAffecteesVeille[$sejour_id]->_ref_patient =& $listPats[$sejourNonAffectesVeille[$sejour_id]->patient_id];
   }
   else {
     $sejourNonAffectesVeille[$sejour_id]->loadRefPatient();
     $listPats[$sejourNonAffectesVeille[$sejour_id]->patient_id] =& $sejourNonAffectesVeille[$sejour_id]->_ref_patient;
   }
   $sejourNonAffectesVeille[$sejour_id]->loadRefsOperations();
   foreach($sejourNonAffectesVeille[$sejour_id]->_ref_operations as $operation_id => $operation) {
     $sejourNonAffectesVeille[$sejour_id]->_ref_operations[$operation_id]->loadRefCCAM();
   }
}

// Admissions du matin
$where = array(
  "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date $heureLimit'",
  "type" => "!= 'exte'",
  "annule" => "= 0",
  $ljwhere  
);
$sejourNonAffectesMatin = new CSejour;
$sejourNonAffectesMatin = $sejourNonAffectesMatin->loadList($where, $order, null, null, $leftjoin);

foreach ($sejourNonAffectesMatin as $sejour_id => $sejour) {
   if(isset($listChirs[$sejourNonAffectesMatin[$sejour_id]->praticien_id])) {
     $sejourNonAffectesMatin[$sejour_id]->_ref_praticien =& $listChirs[$sejourNonAffectesMatin[$sejour_id]->praticien_id];
   }
   else {
     $sejourNonAffectesMatin[$sejour_id]->loadRefPraticien();
     $sejourNonAffectesMatin[$sejour_id]->_ref_praticien->_ref_function =& $listFunctions[$sejourNonAffectesMatin[$sejour_id]->_ref_praticien->function_id];
     $listChirs[$sejourNonAffectesMatin[$sejour_id]->praticien_id] =& $sejourNonAffectesMatin[$sejour_id]->_ref_praticien;
   }
   if(isset($listPats[$sejourNonAffectesMatin[$sejour_id]->patient_id])) {
     $sejourNonAffectesMatin[$sejour_id]->_ref_patient =& $listPats[$sejourNonAffectesMatin[$sejour_id]->patient_id];
   }
   else {
     $sejourNonAffectesMatin[$sejour_id]->loadRefPatient();
     $listPats[$sejourNonAffectesMatin[$sejour_id]->patient_id] =& $sejourNonAffectesMatin[$sejour_id]->_ref_patient;
   }
   $sejourNonAffectesMatin[$sejour_id]->loadRefsOperations();
   foreach($sejourNonAffectesMatin[$sejour_id]->_ref_operations as $operation_id => $operation) {
     $sejourNonAffectesMatin[$sejour_id]->_ref_operations[$operation_id]->loadRefCCAM();
   }
}

// Admissions du soir
$where = array(
  "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
  "type" => "!= 'exte'",
  "annule" => "= 0",
  $ljwhere  
);
$sejourNonAffectesSoir = new CSejour;
$sejourNonAffectesSoir = $sejourNonAffectesSoir->loadList($where, $order, null, null, $leftjoin);

foreach ($sejourNonAffectesSoir as $sejour_id => $sejour) {
   if(isset($listChirs[$sejourNonAffectesSoir[$sejour_id]->praticien_id])) {
     $sejourNonAffectesSoir[$sejour_id]->_ref_praticien =& $listChirs[$sejourNonAffectesSoir[$sejour_id]->praticien_id];
   }
   else {
     $sejourNonAffectesSoir[$sejour_id]->loadRefPraticien();
     $sejourNonAffectesSoir[$sejour_id]->_ref_praticien->_ref_function =& $listFunctions[$sejourNonAffectesSoir[$sejour_id]->_ref_praticien->function_id];
     $listChirs[$sejourNonAffectesSoir[$sejour_id]->praticien_id] =& $sejourNonAffectesSoir[$sejour_id]->_ref_praticien;
   }
   if(isset($listPats[$sejourNonAffectesSoir[$sejour_id]->patient_id])) {
     $sejourNonAffectesSoir[$sejour_id]->_ref_patient =& $listPats[$sejourNonAffectesSoir[$sejour_id]->patient_id];
   }
   else {
     $sejourNonAffectesSoir[$sejour_id]->loadRefPatient();
     $listPats[$sejourNonAffectesSoir[$sejour_id]->patient_id] =& $sejourNonAffectesSoir[$sejour_id]->_ref_patient;
   }
   $sejourNonAffectesSoir[$sejour_id]->loadRefsOperations();
   foreach($sejourNonAffectesSoir[$sejour_id]->_ref_operations as $operation_id => $operation) {
     $sejourNonAffectesSoir[$sejour_id]->_ref_operations[$operation_id]->loadRefCCAM();
   }
}

// Admissions antérieures
$where = array(
  "annule" => "= 0",
  "'".mbDate("-2 DAYS", $date)."' BETWEEN entree_prevue AND sortie_prevue",
  $ljwhere
);
$sejourNonAffectesAvant = new CSejour;
$sejourNonAffectesAvant = $sejourNonAffectesAvant->loadList($where, $order, null, null, $leftjoin);

foreach ($sejourNonAffectesAvant as $sejour_id => $sejour) {
   if(isset($listChirs[$sejourNonAffectesAvant[$sejour_id]->praticien_id])) {
     $sejourNonAffectesAvant[$sejour_id]->_ref_praticien =& $listChirs[$sejourNonAffectesAvant[$sejour_id]->praticien_id];
   }
   else {
     $sejourNonAffectesAvant[$sejour_id]->loadRefPraticien();
     $sejourNonAffectesAvant[$sejour_id]->_ref_praticien->_ref_function =& $listFunctions[$sejourNonAffectesAvant[$sejour_id]->_ref_praticien->function_id];
     $listChirs[$sejourNonAffectesAvant[$sejour_id]->praticien_id] =& $sejourNonAffectesAvant[$sejour_id]->_ref_praticien;
   }
   if(isset($listPats[$sejourNonAffectesAvant[$sejour_id]->patient_id])) {
     $sejourNonAffectesAvant[$sejour_id]->_ref_patient =& $listPats[$sejourNonAffectesAvant[$sejour_id]->patient_id];
   }
   else {
     $sejourNonAffectesAvant[$sejour_id]->loadRefPatient();
     $listPats[$sejourNonAffectesAvant[$sejour_id]->patient_id] =& $sejourNonAffectesAvant[$sejour_id]->_ref_patient;
   }
   $sejourNonAffectesAvant[$sejour_id]->loadRefsOperations();
   foreach($sejourNonAffectesAvant[$sejour_id]->_ref_operations as $operation_id => $operation) {
     $sejourNonAffectesAvant[$sejour_id]->_ref_operations[$operation_id]->loadRefCCAM();
   }
}

$groupSejourNonAffectes = array(
  "veille" => $sejourNonAffectesVeille ,
  "matin"  => $sejourNonAffectesMatin ,
  "soir"   => $sejourNonAffectesSoir ,
  "avant"  => $sejourNonAffectesAvant
);

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->debugging = false;
$smarty->assign('pathos' , $pathos);
$smarty->assign('date' , $date );
$smarty->assign('demain', mbDate("+ 1 day", $date));
$smarty->assign('heureLimit', $heureLimit);
$smarty->assign('mode', $mode);
$smarty->assign('totalLits', $totalLits);
$smarty->assign('services', $services);
$smarty->assign('groupSejourNonAffectes' , $groupSejourNonAffectes);

$smarty->display('vw_affectations.tpl');
?>