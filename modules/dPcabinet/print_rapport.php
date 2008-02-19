<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// !! Attention, régression importante si ajout de type de paiement
global $AppUI, $can, $m;

$ds = CSQLDataSource::get("std");
$today = mbDate();
$compta = mbGetValueFromGet("compta", '0');
$cs = mbGetValueFromGetOrSession("cs");

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate());
$filter->_date_max = mbGetValueFromGetOrSession("_date_max", mbDate());
$filter->_etat_reglement_patient = mbGetValueFromGetOrSession("_etat_reglement_patient");
$filter->_etat_reglement_tiers = mbGetValueFromGetOrSession("_etat_reglement_tiers");

$type = $filter->patient_mode_reglement = mbGetValueFromGetOrSession("patient_mode_reglement", 0);
if($type == null) {
	$type = 0;
}
$aff = $filter->_type_affichage  = mbGetValueFromGetOrSession("_type_affichage" , 1);
//Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if($aff == "complete") {
	$aff = 1;
} elseif ($aff == "totaux"){
	$aff = 0;
}

$chir = mbGetValueFromGetOrSession("chir");
$chirSel = new CMediusers;
$chirSel->load($chir);

// Requète sur les plages de consultation considérées
$where = array();
$ljoin = array();
if($compta) {
  $filter->_etat_reglement_patient = "";
  $filter->_etat_reglement_tiers   = "";
  
  $where[] = "(consultation.patient_date_reglement >= '$filter->_date_min' AND consultation.patient_date_reglement <= '$filter->_date_max')
               OR (consultation.tiers_date_reglement >= '$filter->_date_min' AND consultation.tiers_date_reglement <= '$filter->_date_max')";
  
  $where[] = "(consultation.du_patient > 0) OR (consultation.du_tiers > 0)";
  if($type){
    $where["consultation.patient_mode_reglement"] = "= '$type'";
  }
  $ljoin["consultation"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";
} else {
  $where[] = "date >= '$filter->_date_min'";
  $where[] = "date <= '$filter->_date_max'";
}

// Chargement des plages
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);
$where["chir_id"] = $ds->prepareIn(array_keys($listPrat), $chir);
$listPlage = new CPlageconsult;
$listPlage = $listPlage->loadList($where, "date, debut, chir_id", null, null, $ljoin);


// Initialisation du tableau de reglement Patients
$reglement["cheque"]     = array("somme" => "0", "du_patient" => "0", "du_tiers" => "0", "nb_reglement_patient" => "0", "nb_reglement_tiers" => "0");
$reglement["CB"]         = array("somme" => "0", "du_patient" => "0", "du_tiers" => "0", "nb_reglement_patient" => "0", "nb_reglement_tiers" => "0");
$reglement["especes"]    = array("somme" => "0", "du_patient" => "0", "du_tiers" => "0", "nb_reglement_patient" => "0", "nb_reglement_tiers" => "0");
$reglement["virement"]   = array("somme" => "0", "du_patient" => "0", "du_tiers" => "0", "nb_reglement_patient" => "0", "nb_reglement_tiers" => "0");
$reglement["autre"]      = array("somme" => "0", "du_patient" => "0", "du_tiers" => "0", "nb_reglement_patient" => "0", "nb_reglement_tiers" => "0");
$reglement["non_reglee"] = array("somme" => "0", "du_patient" => "0", "du_tiers" => "0", "nb_reglement_patient" => "0", "nb_reglement_tiers" => "0");

// Tableau recapitulatif
$recapitulatif["total_secteur1"]           = 0;
$recapitulatif["total_secteur2"]           = 0;
$recapitulatif["nb_non_reglee_patient"]    = 0;
$recapitulatif["somme_non_reglee_patient"] = 0;
$recapitulatif["nb_non_reglee_tiers"]      = 0;
$recapitulatif["somme_non_reglee_tiers"]   = 0;
$recapitulatif["somme_patient"]            = 0;
$recapitulatif["somme_tiers"]              = 0;
$recapitulatif["nb_patient"]               = 0;
$recapitulatif["nb_tiers"]                 = 0;

// Parcours des plages de consultations
foreach($listPlage as $key => $value) {
  $listPlage[$key]->loadRefsFwd();
  $where = array();
  $where["plageconsult_id"] = "= '".$value->plageconsult_id."'";
  $where["chrono"] = ">= '".CConsultation::TERMINE."'";
  $where["annule"] = "= '0'";
  $where[] = "tarif IS NOT NULL AND tarif <> ''";
  
  // Seulement tenir compte des consultations dont le montant est fixé (validé).
  $where["valide"] = " = '1'";
  
  if($compta){
    $where[] = "(consultation.patient_date_reglement >= '$filter->_date_min' AND consultation.patient_date_reglement <= '$filter->_date_max')
               OR (consultation.tiers_date_reglement >= '$filter->_date_min' AND consultation.tiers_date_reglement <= '$filter->_date_max')";
  }
  
  
  // Facture réglée par le patient
  if($filter->_etat_reglement_patient == "non_reglee"){
    $where["du_patient"] = " != '0'";
    $where["patient_date_reglement"] = " IS NULL";
  }
  // Facture non réglée par le patient
  if($filter->_etat_reglement_patient == "reglee"){
    $where["patient_date_reglement"] = " IS NOT NULL";
  }
  
  // Facture non reglée par tiers payant
  if($filter->_etat_reglement_tiers == "non_reglee"){
    $where[] = "`tiers_date_reglement` IS NULL";
    $where["du_tiers"] = " != '0' ";
  } 
  // Facture reglée par tiers payant
  if($filter->_etat_reglement_tiers == "reglee"){
    $where["tiers_date_reglement"] = " IS NOT NULL";
  }
  
  if($type){
    $where["patient_mode_reglement"] = "= '$type'";
  }
  // Ne pas prendre en compte les prises en charge aux urgences
  $where["sejour_id"] = "IS NULL";
  $listConsult = new CConsultation;
  
  $listConsult = $listConsult->loadList($where, "heure");
  $listPlage[$key]->_ref_consultations = $listConsult;
  $listPlage[$key]->total1 = 0;
  $listPlage[$key]->total2 = 0;
  
  // Parcours des consultations
  foreach($listPlage[$key]->_ref_consultations as $key2 => $consult) {
    if($cs == "0" && $consult->_somme == 0){
      unset($listPlage[$key]->_ref_consultations[$key2]);
    }
    // Chargement du patient de la consultation
    $consult->loadRefPatient();
     
   if($consult->du_patient){
     @$reglement[$consult->patient_mode_reglement]["du_patient"] += $consult->du_patient; 
   }
   if($consult->du_tiers){
     @$reglement[$consult->tiers_mode_reglement]["du_tiers"]     += $consult->du_tiers;
   }
   if($consult->patient_mode_reglement && $consult->du_patient){
     $reglement[$consult->patient_mode_reglement]["nb_reglement_patient"]++;
   }
   if($consult->tiers_mode_reglement && $consult->du_tiers){
     $reglement[$consult->tiers_mode_reglement]["nb_reglement_tiers"]++;
   }
 
    $listPlage[$key]->total1 += $consult->secteur1;
    $listPlage[$key]->total2 += $consult->secteur2;
    $listPlage[$key]->du_patient += $consult->du_patient;  
    $listPlage[$key]->du_tiers += $consult->du_tiers;
    
    // Montant non réglé par le patient
    if(!$consult->patient_date_reglement && $consult->du_patient){
      $recapitulatif["nb_non_reglee_patient"]++;
      $recapitulatif["somme_non_reglee_patient"] += $consult->du_patient;
    }
    $recapitulatif["somme_patient"] += $consult->du_patient;
    if($consult->du_patient){
      $recapitulatif["nb_patient"]++;
    }
    
    // Montant non regle par le tiers
    if(!$consult->tiers_date_reglement && $consult->du_tiers){
      $recapitulatif["nb_non_reglee_tiers"]++;
      $recapitulatif["somme_non_reglee_tiers"] += $consult->du_tiers; 
    }
    $recapitulatif["somme_tiers"] += $consult->du_tiers;
    if($consult->du_tiers){
      $recapitulatif["nb_tiers"]++;
    }
  
    $recapitulatif["total_secteur1"] += $consult->secteur1;
    $recapitulatif["total_secteur2"] += $consult->secteur2;
  }
  
  if(!count($listPlage[$key]->_ref_consultations)){
    unset($listPlage[$key]);
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("recapitulatif"      , $recapitulatif);
$smarty->assign("reglement"          , $reglement);


$smarty->assign("cs"                 , $cs);
$smarty->assign("compta"             , $compta);
$smarty->assign("today"              , $today);
$smarty->assign("filter"             , $filter);
$smarty->assign("aff"                , $aff);
$smarty->assign("_etat_reglement_patient", $filter->_etat_reglement_patient);
$smarty->assign("_etat_reglement_tiers"  , $filter->_etat_reglement_tiers);
$smarty->assign("type"               , $type);
$smarty->assign("chirSel"            , $chirSel);
$smarty->assign("listPlage"          , $listPlage);

$smarty->display("print_rapport.tpl");

?>