<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

// Utilisateur sélectionné ou utilisateur courant
$prat_id      = CValue::getOrSession("chirSel", 0);
$selConsult   = CValue::getOrSession("selConsult", null);

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

$consult = new CConsultation();

// Test compliqué afin de savoir quelle consultation charger
if(isset($_GET["selConsult"])) {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  } else {
    $consult = new CConsultation();
    $selConsult = null;
    CValue::setSession("selConsult");
  }
} else {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      CValue::setSession("selConsult");
    }
  }
}

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChir = $userSel->loadPraticiens(PERM_EDIT);
} else {
  $listChir = $userSel->loadProfessionnelDeSante(PERM_EDIT);
}

if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un personnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

// Consultation courante
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  // Some Forward references
  $consult->loadRefsFwd();
  $consult->loadRefConsultAnesth();
    
  // Patient
  $patient =& $consult->_ref_patient;
}
$consult->loadIdsFSE();
$consult->makeFSE();
$consult->_ref_chir->loadIdCPS();
$consult->_ref_patient->loadIdVitale();

// Récupération des tarifs
$tarif = new CTarif;
$tarifs = array();
if (!$consult->tarif || $consult->tarif == "pursue") {
	$order = "description";
	$where = array();
	$where["chir_id"] = "= '$userSel->user_id'";
	$tarifs["user"] = $tarif->loadList($where, $order);
	foreach ($tarifs["user"] as $_tarif) {
	  $_tarif->getPrecodeReady();
	}
	
	$where = array();
	$where["function_id"] = "= '$userSel->function_id'";
	$tarifs["func"] = $tarif->loadList($where, $order);
	foreach($tarifs["func"] as $_tarif) {
	  $_tarif->getPrecodeReady();
	}
}

// Règlements
$consult->loadRefsReglements();

// Reglement vide pour le formulaire
$reglement = new CReglement();
$reglement->montant = round($consult->_du_patient_restant, 2);

// Codes et actes NGAP
$consult->loadRefsActesNGAP();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banques"  , $banques);
$smarty->assign("consult"  , $consult);
$smarty->assign("reglement", $reglement);
$smarty->assign("tarifs"   , $tarifs);

$smarty->display("inc_vw_reglement.tpl");

?>