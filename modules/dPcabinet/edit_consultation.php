<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
	$AppUI->redirect("m=system&a=access_denied");
}

$date  = mbGetValueFromGetOrSession("date", mbDate());
$vue   = mbGetValueFromGetOrSession("vue2", 0);
$today = mbDate();
$hour  = mbTime(null);
$_is_anesth = false;

$prat_id    = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$selConsult = mbGetValueFromGetOrSession("selConsult", null);

$consult = new CConsultation();

if(isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", null);
}

// Test compliqué afin de savoir quelle consultation charger
if(isset($_GET["selConsult"])) {
  if($consult->load($selConsult)) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    $selConsult = null;
    mbSetValueToSession("selConsult");
  }
} else {
  if($consult->load($selConsult)) {
    $consult->loadRefsFwd();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      mbSetValueToSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  mbSetValueToSession("chirSel", 0);
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Consultation courante
$consult->_ref_chir =& $userSel;
if($consult->consultation_id) {
  $consult->loadRefs();
  $consult->loadAides($userSel->user_id);
  $consult->_ref_consult_anesth->loadAides($userSel->user_id);
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadStaticCIM10($userSel->user_id);
  foreach($patient->_ref_consultations as $key => $consul) {
    $patient->_ref_consultations[$key]->loadRefsFwd();
  }
  foreach($patient->_ref_sejours as $key => $sejour) {
    $patient->_ref_sejours[$key]->loadRefsFwd();
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
} else {
  $consult->_ref_consult_anesth->consultation_anesth_id = 0;
}

// Récupération des modèles
$whereCommon = array();
if($consult->_ref_consult_anesth->consultation_anesth_id){
  $whereCommon[] = "`type` = 'consultAnesth'";
}else{
  $whereCommon[] = "`type` = 'consultation'";
}

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

// Chargement des aides à la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($userSel->user_id);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);

$techniquesComp = new CTechniqueComp();
$techniquesComp->loadAides($userSel->user_id);

$examComp = new CExamComp();
$examComp->loadAides($userSel->user_id);

// Vérification du cas anesthésie
if($consult->_ref_chir->isFromType(array("Anesthésiste"))) {
  $_is_anesth=true; 
} else {
  $_is_anesth=false;
}
// Création du template
$smarty = new CSmartyDP(1);
$smarty->assign("date"          , $date);
$smarty->assign("hour"          , $hour);
$smarty->assign("vue"           , $vue);
$smarty->assign("today"         , $today);
$smarty->assign("userSel"       , $userSel);
$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("tarifsChir"    , $tarifsChir);
$smarty->assign("tarifsCab"     , $tarifsCab);
$smarty->assign("anesth"        , $anesth);
$smarty->assign("consult"       , $consult);
$smarty->assign("antecedent"    , $antecedent);
$smarty->assign("traitement"    , $traitement);
$smarty->assign("techniquesComp", $techniquesComp);
$smarty->assign("examComp"      , $examComp);
$smarty->assign("_is_anesth"    , $_is_anesth);  

if($_is_anesth) {
  $secs = array();
  for ($i = 0; $i < 60; $i++) {
    $secs[] = $i;
  }
  $mins = array();
  for ($i = 0; $i < 15; $i++) {
    $mins[] = $i;
  }
  
  $smarty->assign("secs"          , $secs);
  $smarty->assign("mins"          , $mins);
  $smarty->assign("consult_anesth", $consult->_ref_consult_anesth);
  $smarty->display("edit_consultation_anesth.tpl");
} else {
  $smarty->display("edit_consultation_old.tpl");
}
?>