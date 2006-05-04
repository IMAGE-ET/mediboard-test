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
$listPlagesOp = array();

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

          $operation =& $affectations[$affectation_id]->_ref_operation;
          if(isset($listChirs[$operation->chir_id])) {
            $operation->_ref_chir =& $listChirs[$operation->chir_id];
          }
          else {
            $operation->loadRefChir();
            $operation->_ref_chir->_ref_function =& $listFunctions[$operation->_ref_chir->function_id];
            $listChirs[$operation->chir_id] =& $operation->_ref_chir;
          }
          if(isset($listPats[$operation->pat_id])) {
            $operation->_ref_pat =& $listPats[$operation->pat_id];
          }
          else {
            $operation->loadRefPat();
            $listPats[$operation->pat_id] =& $operation->_ref_pat;
          }
          if(isset($listPlagesOp[$operation->plageop_id])) {
            $operation->_ref_plageop =& $listPlagesOp[$operation->plageop_id];
          }
          else {
            $operation->loadRefPlageOp();
            $listPlagesOp[$operation->plageop_id] =& $operation->_ref_plageop;
          }
          $operation->loadRefCCAM();
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
  "affectation"     => "operations.operation_id = affectation.operation_id",
  "users_mediboard" => "operations.chir_id = users_mediboard.user_id",
  "patients" => "operations.pat_id = patients.patient_id"
);
$ljwhere = "affectation.affectation_id IS NULL";
$order = "users_mediboard.function_id, operations.date_adm, operations.time_adm, patients.nom, patients.prenom";

// Admissions de la veille
$where = array(
  "date_adm" => "= '".mbDate("-1 days", $date)."'",
  "type_adm" => "!= 'exte'",
  "annulee" => "= 0",
  $ljwhere  
);
$opNonAffecteesVeille = new COperation;
$opNonAffecteesVeille = $opNonAffecteesVeille->loadList($where, $order, null, null, $leftjoin);

foreach ($opNonAffecteesVeille as $op_id => $op) {
   if(isset($listChirs[$opNonAffecteesVeille[$op_id]->chir_id])) {
     $opNonAffecteesVeille[$op_id]->_ref_chir =& $listChirs[$opNonAffecteesVeille[$op_id]->chir_id];
   }
   else {
     $opNonAffecteesVeille[$op_id]->loadRefChir();
     $opNonAffecteesVeille[$op_id]->_ref_chir->_ref_function =& $listFunctions[$opNonAffecteesVeille[$op_id]->_ref_chir->function_id];
     $listChirs[$opNonAffecteesVeille[$op_id]->chir_id] =& $opNonAffecteesVeille[$op_id]->_ref_chir;
   }
   if(isset($listPats[$opNonAffecteesVeille[$op_id]->pat_id])) {
     $opNonAffecteesVeille[$op_id]->_ref_pat =& $listPats[$opNonAffecteesVeille[$op_id]->pat_id];
   }
   else {
     $opNonAffecteesVeille[$op_id]->loadRefPat();
     $listPats[$opNonAffecteesVeille[$op_id]->pat_id] =& $opNonAffecteesVeille[$op_id]->_ref_pat;
   }
   if(isset($listPlagesOp[$opNonAffecteesVeille[$op_id]->plageop_id])) {
     $opNonAffecteesVeille[$op_id]->_ref_plageop =& $listPlagesOp[$opNonAffecteesVeille[$op_id]->plageop_id];
   }
   else {
     $opNonAffecteesVeille[$op_id]->loadRefPlageOp();
     $listPlagesOp[$opNonAffecteesVeille[$op_id]->plageop_id] =& $opNonAffecteesVeille[$op_id]->_ref_plageop;
   }
   $opNonAffecteesVeille[$op_id]->loadRefCCAM();
}

// Admissions du matin
$where = array(
  "date_adm" => "= '$date'",
  "time_adm" => "< '$heureLimit'",
  "type_adm" => "!= 'exte'",
  "annulee" => "= 0",
  $ljwhere  
);
$opNonAffecteesMatin = new COperation;
$opNonAffecteesMatin = $opNonAffecteesMatin->loadList($where, $order, null, null, $leftjoin);

foreach ($opNonAffecteesMatin as $op_id => $op) {
   if(isset($listChirs[$opNonAffecteesMatin[$op_id]->chir_id])) {
     $opNonAffecteesMatin[$op_id]->_ref_chir =& $listChirs[$opNonAffecteesMatin[$op_id]->chir_id];
   }
   else {
     $opNonAffecteesMatin[$op_id]->loadRefChir();
     $opNonAffecteesMatin[$op_id]->_ref_chir->_ref_function =& $listFunctions[$opNonAffecteesMatin[$op_id]->_ref_chir->function_id];
     $listChirs[$opNonAffecteesMatin[$op_id]->chir_id] =& $opNonAffecteesMatin[$op_id]->_ref_chir;
   }
   if(isset($listPats[$opNonAffecteesMatin[$op_id]->pat_id])) {
     $opNonAffecteesMatin[$op_id]->_ref_pat =& $listPats[$opNonAffecteesMatin[$op_id]->pat_id];
   }
   else {
     $opNonAffecteesMatin[$op_id]->loadRefPat();
     $listPats[$opNonAffecteesMatin[$op_id]->pat_id] =& $opNonAffecteesMatin[$op_id]->_ref_pat;
   }
   if(isset($listPlagesOp[$opNonAffecteesMatin[$op_id]->plageop_id])) {
     $opNonAffecteesMatin[$op_id]->_ref_plageop =& $listPlagesOp[$opNonAffecteesMatin[$op_id]->plageop_id];
   }
   else {
     $opNonAffecteesMatin[$op_id]->loadRefPlageOp();
     $listPlagesOp[$opNonAffecteesMatin[$op_id]->plageop_id] =& $opNonAffecteesMatin[$op_id]->_ref_plageop;
   }
   $opNonAffecteesMatin[$op_id]->loadRefCCAM();
}

// Admissions du soir
$where = array(
  "date_adm" => "= '$date'",
  "time_adm" => ">= '$heureLimit'",
  "type_adm" => "!= 'exte'",
  "annulee" => "= 0",
  $ljwhere  
);
$opNonAffecteesSoir = new COperation;
$opNonAffecteesSoir = $opNonAffecteesSoir->loadList($where, $order, null, null, $leftjoin);

foreach ($opNonAffecteesSoir as $op_id => $op) {
   if(isset($listChirs[$opNonAffecteesSoir[$op_id]->chir_id])) {
     $opNonAffecteesSoir[$op_id]->_ref_chir =& $listChirs[$opNonAffecteesSoir[$op_id]->chir_id];
   }
   else {
     $opNonAffecteesSoir[$op_id]->loadRefChir();
     $opNonAffecteesSoir[$op_id]->_ref_chir->_ref_function =& $listFunctions[$opNonAffecteesSoir[$op_id]->_ref_chir->function_id];
     $listChirs[$opNonAffecteesSoir[$op_id]->chir_id] =& $opNonAffecteesSoir[$op_id]->_ref_chir;
   }
   if(isset($listPats[$opNonAffecteesSoir[$op_id]->pat_id])) {
     $opNonAffecteesSoir[$op_id]->_ref_pat =& $listPats[$opNonAffecteesSoir[$op_id]->pat_id];
   }
   else {
     $opNonAffecteesSoir[$op_id]->loadRefPat();
     $listPats[$opNonAffecteesSoir[$op_id]->pat_id] =& $opNonAffecteesSoir[$op_id]->_ref_pat;
   }
   if(isset($listPlagesOp[$opNonAffecteesSoir[$op_id]->plageop_id])) {
     $opNonAffecteesSoir[$op_id]->_ref_plageop =& $listPlagesOp[$opNonAffecteesSoir[$op_id]->plageop_id];
   }
   else {
     $opNonAffecteesSoir[$op_id]->loadRefPlageOp();
     $listPlagesOp[$opNonAffecteesSoir[$op_id]->plageop_id] =& $opNonAffecteesSoir[$op_id]->_ref_plageop;
   }
   $opNonAffecteesSoir[$op_id]->loadRefCCAM();
}

// Admissions antérieures
$where = array(
  "annulee" => "= 0",
  "'$date' BETWEEN ADDDATE(`date_adm`, INTERVAL 2 DAY) AND ADDDATE(`date_adm`, INTERVAL `duree_hospi` DAY)",
  "affectation.affectation_id IS NULL"
);
$opNonAffecteesAvant = new COperation;
$opNonAffecteesAvant = $opNonAffecteesAvant->loadList($where, $order, null, null, $leftjoin);

foreach ($opNonAffecteesAvant as $op_id => $op) {
   if(isset($listChirs[$opNonAffecteesAvant[$op_id]->chir_id])) {
     $opNonAffecteesAvant[$op_id]->_ref_chir =& $listChirs[$opNonAffecteesAvant[$op_id]->chir_id];
   }
   else {
     $opNonAffecteesAvant[$op_id]->loadRefChir();
     $opNonAffecteesAvant[$op_id]->_ref_chir->_ref_function =& $listFunctions[$opNonAffecteesAvant[$op_id]->_ref_chir->function_id];
     $listChirs[$opNonAffecteesAvant[$op_id]->chir_id] =& $opNonAffecteesAvant[$op_id]->_ref_chir;
   }
   if(isset($listPats[$opNonAffecteesAvant[$op_id]->pat_id])) {
     $opNonAffecteesAvant[$op_id]->_ref_pat =& $listPats[$opNonAffecteesAvant[$op_id]->pat_id];
   }
   else {
     $opNonAffecteesAvant[$op_id]->loadRefPat();
     $listPats[$opNonAffecteesAvant[$op_id]->pat_id] =& $opNonAffecteesAvant[$op_id]->_ref_pat;
   }
   if(isset($listPlagesOp[$opNonAffecteesAvant[$op_id]->plageop_id])) {
     $opNonAffecteesAvant[$op_id]->_ref_plageop =& $listPlagesOp[$opNonAffecteesAvant[$op_id]->plageop_id];
   }
   else {
     $opNonAffecteesAvant[$op_id]->loadRefPlageOp();
     $listPlagesOp[$opNonAffecteesAvant[$op_id]->plageop_id] =& $opNonAffecteesAvant[$op_id]->_ref_plageop;
   }
   $opNonAffecteesAvant[$op_id]->loadRefCCAM();
}

$groupOpNonAffectees = array(
  "veille" => $opNonAffecteesVeille ,
  "matin"  => $opNonAffecteesMatin ,
  "soir"   => $opNonAffecteesSoir ,
  "avant"  => $opNonAffecteesAvant
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
$smarty->assign('groupOpNonAffectees' , $groupOpNonAffectees);

$smarty->display('vw_affectations.tpl');
?>