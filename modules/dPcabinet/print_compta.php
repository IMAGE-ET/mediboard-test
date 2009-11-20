<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// !! Attention, régression importante si ajout de type de paiement
global $AppUI, $can, $m;

$today = mbDate();

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = CValue::getOrSession("_date_min", mbDate());
$filter->_date_max = CValue::getOrSession("_date_max", mbDate());

$filter->_mode_reglement = CValue::getOrSession("mode", 0);

$filter->_type_affichage  = CValue::getOrSession("_type_affichage" , 1);
//Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if($filter->_type_affichage == "complete") {
	$filter->_type_affichage = 1;
} elseif ($filter->_type_affichage == "totaux"){
	$filter->_type_affichage = 0;
}

// On recherche tous les règlements effectués selon les critères
$ljoin = array();
$ljoin["consultation"] = "reglement.consultation_id = consultation.consultation_id";
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

$where = array();
$where[] = "DATE(reglement.date) >= '$filter->_date_min' AND DATE(reglement.date) <= '$filter->_date_max'";

// Tri sur les modes de paiement
if ($filter->_mode_reglement) {
  $where["reglement.mode"] = "= '$filter->_mode_reglement'";
}

// Tri sur les praticiens
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

$prat = new CMediusers;
$prat->load(CValue::getOrSession("chir"));
$prat->loadRefFunction();
if ($prat->_id) {
  $listPrat = array($prat->_id => $prat);
}
else {
  $listPrat = $prat->loadPraticiens(PERM_EDIT, $mediuser->isAdmin() ? null : $mediuser->function_id);
}

$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));

// Chargement
$reglement = new CReglement();
$reglements = $reglement->loadList($where, "reglement.date, plageconsult.chir_id", null, null, $ljoin);

// Calcul du récapitulatif
// Initialisation du tableau de reglements
$recapReglement["total"] = array(
  "du_patient"           => "0",
  "du_tiers"             => "0",
  "nb_reglement_patient" => "0",
  "nb_reglement_tiers"   => "0",
  "secteur1"             => "0",
  "secteur2"             => "0",
);
foreach (array_merge($reglement->_specs["mode"]->_list, array("")) as $_mode) {
  $recapReglement[$_mode] = array(
   "du_patient"           => "0",
   "du_tiers"             => "0",
   "nb_reglement_patient" => "0",
   "nb_reglement_tiers"   => "0",
  );
}

$listReglements = array();
$listConsults = array();
foreach ($reglements as $_reglement) {
  $_reglement->loadRefConsultation();
  $_reglement->_ref_consultation->loadRefPatient(1);
  $_reglement->_ref_consultation->loadRefPlageConsult(1);
	
  if ($_reglement->emetteur == "patient") {
    $recapReglement["total"]["du_patient"] += $_reglement->montant;
    $recapReglement["total"]["nb_reglement_patient"]++;
    $recapReglement[$_reglement->mode]["du_patient"] += $_reglement->montant;
    $recapReglement[$_reglement->mode]["nb_reglement_patient"]++;
  } 
	else {
    $recapReglement["total"]["du_tiers"] += $_reglement->montant;
    $recapReglement["total"]["nb_reglement_tiers"]++;
    $recapReglement[$_reglement->mode]["du_tiers"] += $_reglement->montant;
    $recapReglement[$_reglement->mode]["nb_reglement_tiers"]++;
  }
	
  if(!array_key_exists($_reglement->_ref_consultation->_id, $listConsults)) {
    $recapReglement["total"]["secteur1"] += $_reglement->_ref_consultation->secteur1;
    $recapReglement["total"]["secteur2"] += $_reglement->_ref_consultation->secteur2;
    $listConsults[$_reglement->_ref_consultation->_id] = $_reglement->_ref_consultation;
  }
  if(!isset($listReglements[mbDate(null, $_reglement->date)])) {
    $listReglements[mbDate(null, $_reglement->date)]["total"]["patient"] = 0;
    $listReglements[mbDate(null, $_reglement->date)]["total"]["tiers"] = 0;
    $listReglements[mbDate(null, $_reglement->date)]["total"]["total"] = 0;
  }
	
  $listReglements[mbDate(null, $_reglement->date)]["total"][$_reglement->emetteur] += $_reglement->montant;
  $listReglements[mbDate(null, $_reglement->date)]["total"]["total"] += $_reglement->montant;
  
  $listReglements[mbDate(null, $_reglement->date)]["reglements"][$_reglement->_id] = $_reglement;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"              , $today);
$smarty->assign("filter"             , $filter);
$smarty->assign("listPrat"           , $listPrat);
$smarty->assign("listReglements"     , $listReglements);
$smarty->assign("listConsults"       , $listConsults);
$smarty->assign("recapReglement"     , $recapReglement);

$smarty->display("print_compta.tpl");

?>