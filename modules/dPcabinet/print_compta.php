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

$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

$chir = CValue::getOrSession("chir");
$chirSel = new CMediusers;
$chirSel->load($chir);

$reglement = new CReglement();

// Initialisation du tableau de reglements
$recapReglement["total"]      = array("du_patient"           => "0",
                                      "du_tiers"             => "0",
                                      "nb_reglement_patient" => "0",
                                      "nb_reglement_tiers"   => "0",
                                      "secteur1"             => "0",
                                      "secteur2"             => "0");
foreach(explode("|", $reglement->_specs["mode"]->list) as $curr_type) {
  $recapReglement[$curr_type] = array("du_patient"           => "0",
                                      "du_tiers"             => "0",
                                      "nb_reglement_patient" => "0",
                                      "nb_reglement_tiers"   => "0");
}

// On recherche tous les règlements effectués selon les critères
$where = array();
$ljoin = array();
// Left join sur les consultations
$ljoin["consultation"] = "reglement.consultation_id = consultation.consultation_id";
// Left join sur les plages de consult
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
// Tri sur les dates
$where[] = "DATE(reglement.date) >= '$filter->_date_min' AND DATE(reglement.date) <= '$filter->_date_max'";
// Tri sur les modes de paiement
if($filter->_mode_reglement) {
  $where["reglement.mode"] = "= '$filter->_mode_reglement'";
}
// Tri sur les praticiens
$listPrat = new CMediusers();
$is_admin = in_array(CUser::$types[$mediuser->_user_type], array("Administrator"));
if($is_admin) {
  $listPrat = $listPrat->loadPraticiens(PERM_EDIT);
} else {
  $listPrat = $listPrat->loadPraticiens(PERM_EDIT, $mediuser->function_id);
}
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir);
$reglements = $reglement->loadList($where, "reglement.date, plageconsult.chir_id", null, null, $ljoin);
$listReglements = array();
$listConsults = array();
foreach($reglements as $curr_reglement) {
  $curr_reglement->loadRefsFwd(1);
  $curr_reglement->_ref_consultation->loadRefsFwd(1);
  if($curr_reglement->emetteur == "patient") {
    $recapReglement["total"]["du_patient"] += $curr_reglement->montant;
    $recapReglement["total"]["nb_reglement_patient"]++;
    $recapReglement[$curr_reglement->mode]["du_patient"] += $curr_reglement->montant;
    $recapReglement[$curr_reglement->mode]["nb_reglement_patient"]++;
  } else {
    $recapReglement["total"]["du_tiers"] += $curr_reglement->montant;
    $recapReglement["total"]["nb_reglement_tiers"]++;
    $recapReglement[$curr_reglement->mode]["du_tiers"] += $curr_reglement->montant;
    $recapReglement[$curr_reglement->mode]["nb_reglement_tiers"]++;
  }
  if(!key_exists($curr_reglement->_ref_consultation->_id, $listConsults)) {
    $recapReglement["total"]["secteur1"] += $curr_reglement->_ref_consultation->secteur1;
    $recapReglement["total"]["secteur2"] += $curr_reglement->_ref_consultation->secteur2;
    $listConsults[$curr_reglement->_ref_consultation->_id] = $curr_reglement->_ref_consultation;
  }
  if(!isset($listReglements[mbDate(null, $curr_reglement->date)])) {
    $listReglements[mbDate(null, $curr_reglement->date)]["total"]["patient"] = 0;
    $listReglements[mbDate(null, $curr_reglement->date)]["total"]["tiers"] = 0;
    $listReglements[mbDate(null, $curr_reglement->date)]["total"]["total"] = 0;
  }
  $listReglements[mbDate(null, $curr_reglement->date)]["total"][$curr_reglement->emetteur] += $curr_reglement->montant;
  $listReglements[mbDate(null, $curr_reglement->date)]["total"]["total"] += $curr_reglement->montant;
  
  $listReglements[mbDate(null, $curr_reglement->date)]["reglements"][$curr_reglement->_id] = $curr_reglement;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"              , $today);
$smarty->assign("filter"             , $filter);
$smarty->assign("chirSel"            , $chirSel);
$smarty->assign("listPrat"           , $listPrat);
$smarty->assign("listReglements"     , $listReglements);
$smarty->assign("listConsults"       , $listConsults);
$smarty->assign("recapReglement"     , $recapReglement);

$smarty->display("print_compta.tpl");

?>