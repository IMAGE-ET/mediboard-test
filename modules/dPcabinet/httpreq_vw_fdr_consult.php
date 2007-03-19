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
$prat_id      = mbGetValueFromGetOrSession("chirSel", 0);
$selConsult   = mbGetValueFromGetOrSession("selConsult", null);
$noReglement  = mbGetValueFromGet("noReglement" , 0);

$consult = new CConsultation();

// Test compliqué afin de savoir quelle consultation charger
if(isset($_GET["selConsult"])) {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    $consult = new CConsultation();
    $selConsult = null;
    mbSetValueToSession("selConsult");
  }
} else {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      mbSetValueToSession("selConsult");
    }
  }
}

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

// Consultation courante
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();
  $consult->loadRefsDocs();
  $consult->loadRefsFiles();
  $consult->loadRefsExamAudio();
  $consult->loadRefsExamNyha();
  $consult->loadRefsExamPossum();
}

// Récupération des modèles
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
if($consult->_ref_consult_anesth->consultation_anesth_id){
  $whereCommon[] = "`object_class` = 'CConsultAnesth'";
}else{
  $whereCommon[] = "`object_class` = 'CConsultation'";
}

$order = "nom";

// Modèles de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) { 
  $where = $whereCommon;
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = db_prepare("= %", $userSel->function_id);
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
$smarty = new CSmartyDP();

if($consult->_ref_chir->isFromType(array("Anesthésiste")) || $consult->_ref_consult_anesth->consultation_anesth_id) {
  $_is_anesth=true;	
} else {
  $_is_anesth=false;
}
$smarty->assign("_is_anesth", $_is_anesth);  

$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("tarifsChir"    , $tarifsChir);
$smarty->assign("tarifsCab"     , $tarifsCab);
$smarty->assign("consult"       , $consult);
$smarty->assign("noReglement"   , $noReglement);

$smarty->display("inc_fdr_consult.tpl");

?>