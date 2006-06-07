<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );
require_once( $AppUI->getModuleClass('dPcabinet', 'tarif') );
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );
  
if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$date  = mbGetValueFromGetOrSession("date", mbDate());
$vue   = mbGetValueFromGetOrSession("vue2", 0);
$today = mbDate();
$hour  = mbTime(null);

// Utilisateur sélectionné ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$selConsult = mbGetValueFromGetOrSession("selConsult", 0);
if (isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", 0);
}

//Liste des types d'anesthésie
$anesth = dPgetSysVal("AnesthType");

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
$consult->_ref_consult_anesth->consultation_anesth_id = 0;
if ($selConsult) {
  $consult->load($selConsult);
  $consult->loadRefs();
  $userSel->load($consult->_ref_plageconsult->chir_id);
  $userSel->loadRefs();
  $consult->loadAides($userSel->user_id);
  
  // On vérifie que l'utilisateur a les droits sur la consultation
  $right = false;
  foreach($listChir as $key => $value) {
    if($value->user_id == $consult->_ref_plageconsult->chir_id)
      $right = true;
  }
  if(!$right) {
    $AppUI->setMsg("Vous n'avez pas accès à cette consultation", UI_MSG_ALERT);
    $AppUI->redirect( "m=dPpatients&tab=0&id=$consult->patient_id");
  }
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadStaticCIM10($userSel->user_id);
  foreach ($patient->_ref_consultations as $key => $value) {
    $patient->_ref_consultations[$key]->loadRefsFwd();
  }
  foreach ($patient->_ref_operations as $key => $value) {
    $patient->_ref_operations[$key]->loadRefsFwd();
  }
  foreach ($patient->_ref_hospitalisations as $key => $value) {
    $patient->_ref_hospitalisations[$key]->loadRefsFwd();
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
  
}

// Récupération des modèles
$whereCommon = array();
$whereCommon["type"] = "= 'consultation'";
$order = "nom";

// Modèles de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = "= '$userSel->user_id'";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = "= '$userSel->function_id'";
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// Récupération des tarifs
$order = "description";
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$tarifsChir = new CTarif;
$tarifsChir = $tarifsChir->loadList($where, $order);
$where = array();
$where["function_id"] = "= '$userSel->function_id'";
$tarifsCab = new CTarif;
$tarifsCab = $tarifsCab->loadList($where, $order);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('date', $date );
$smarty->assign('hour', $hour );
$smarty->assign('vue', $vue);
$smarty->assign('today', $today);
$smarty->assign('userSel', $userSel);
$smarty->assign('listModelePrat', $listModelePrat);
$smarty->assign('listModeleFunc', $listModeleFunc);
$smarty->assign('tarifsChir', $tarifsChir);
$smarty->assign('tarifsCab', $tarifsCab);
$smarty->assign('anesth', $anesth);
$smarty->assign('consult', $consult);

if ($consult->_ref_chir->isFromType(array("Anesthésiste")) || $consult->_ref_consult_anesth->consultation_anesth_id) {
  $antecedent = new CAntecedent();
  $antecedent->loadAides($userSel->user_id);
  $smarty->assign("antecedent", $antecedent);

  $traitement = new CTraitement();
  $traitement->loadAides($userSel->user_id);
  $smarty->assign("traitement", $traitement);
  
  $smarty->assign('consult_anesth', $consult->_ref_consult_anesth);
  $smarty->display('edit_consultation_anesth.tpl');
} else {
  $smarty->display('edit_consultation.tpl');
}
?>