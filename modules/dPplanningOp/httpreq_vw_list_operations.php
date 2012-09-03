<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$userSel   = CMediusers::get(CValue::getOrSession("pratSel"));

$date      = CValue::getOrSession("date", mbDate());
$canceled  = CValue::getOrSession("canceled", 0);
$board     = CValue::get("board", 0);
$boardItem = CValue::get("boardItem", 0);

$nb_canceled = 0;

// Urgences du jour
$list_urgences = array();
$operation = new COperation();

if($userSel->_id){
  $operation->date = $date;
  $operation->chir_id = $userSel->_id;
  if(!$canceled) {
    $operation->annulee = 0;
  }
  $list_urgences = $operation->loadMatchingList("annulee, date");
  $operation->annulee = 1;
  $nb_canceled += $operation->countMatchingList();
  foreach($list_urgences as $curr_urg) {
    $curr_urg->canDo();
    $curr_urg->loadRefsFwd();
    $_sejour = $curr_urg->_ref_sejour;
    $curr_urg->loadRefsDocs();
    foreach ($curr_urg->_ref_documents as $_document) {
      $_document->canDo();
    }
    
    $_sejour->loadRefsFwd();
    $_sejour->canDo();
    $_sejour->_ref_patient->loadRefDossierMedical()->countAllergies();
    $_sejour->loadRefsDocs();
    foreach ($_sejour->_ref_documents as $_document) {
      $_document->canDo();
    }
  }
}

// Liste des opérations du jour sélectionné
$list_plages = array();

if($userSel->_id){
  $userSel->loadBackRefs("secondary_functions");
  $secondary_specs = array();
  foreach($userSel->_back["secondary_functions"] as  $curr_sec_spec) {
    $curr_sec_spec->loadRefsFwd();
    $curr_function = $curr_sec_spec->_ref_function;
    $secondary_specs[$curr_function->_id] = $curr_function;
  }
  $where = array();
  $where["date"] = "= '$date'";
  
  $in = "";
  if (count($secondary_specs)) {
    $in = " OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs));
  }
  
  $where[] = "plagesop.chir_id = '$userSel->_id' OR plagesop.anesth_id = '$userSel->_id' OR plagesop.spec_id = '$userSel->function_id' $in";
  $order = "debut, salle_id";
  
  $plageop = new CPlageOp();
  $list_plages = $plageop->loadList($where, $order);
  
  foreach ($list_plages as $_plage) {
    $op_canceled = new COperation();
    $op_canceled->annulee = 1;
    $op_canceled->plageop_id = $_plage->_id;
    $nb_canceled += $op_canceled->countMatchingList();
    
    $_plage->loadRefsFwd();
    
    $where = array();
    if($userSel->_id && !$userSel->isAnesth()) {
      $where["chir_id"] = "= '$userSel->_id'";
    }
    
    $_plage->loadRefsOperations($canceled, "annulee ASC, rank, rank_voulu, horaire_voulu", false, null, $where);
    
    foreach ($_plage->_ref_operations as $_op) {
      $_op->loadRefsFwd();
      $_sejour = $_op->_ref_sejour;
      $_op->loadRefsDocs();
      foreach ($_op->_ref_documents as $_doc) {
        $_doc->canDo();
      }
      $_op->canDo();
      $_sejour->canDo();
      $_sejour->loadRefsFwd();
      $_sejour->_ref_patient->loadRefDossierMedical()->countAllergies();
      $_sejour->loadRefsDocs();
      foreach ($_sejour->_ref_documents as $_doc) {
        $_doc->canDo();
      }
    }
  }
}

// Praticien concerné
$user = CMediusers::get();
if ($user->isPraticien()) {
  $praticien = $user;
} else {
  $praticien = new CMediusers();
  $praticien->load(CValue::getOrSession("pratSel", CValue::getOrSession("praticien_id")));
}

$praticien->loadRefFunction();
$praticien->_ref_function->loadRefGroup();
$praticien->canDo();

// Compter les modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette;

$where = array();
$where['object_class'] = " IN ('COperation', 'CSejour')";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

$nb_modeles_etiquettes = $modele_etiquette->countList($where);

$nb_printers = 0;

if (CModule::getActive("printing")) {
  // Chargement des imprimantes pour l'impression d'étiquettes 
  $user_printers = CMediusers::get();
  $function      = $user_printers->loadRefFunction();
  $nb_printers   = $function->countBackRefs("printers");
}

$compte_rendu = new CCompteRendu();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"     , $praticien);
$smarty->assign("boardItem"     , $boardItem);
$smarty->assign("date"          , $date);
$smarty->assign("canceled"      , $canceled);
$smarty->assign("listUrgences"  , $list_urgences);
$smarty->assign("listDay"       , $list_plages);
$smarty->assign("nb_canceled"   , $nb_canceled);
$smarty->assign("board"         , $board);
$smarty->assign("nb_printers"   , $nb_printers);
$smarty->assign("can_doc"       , $compte_rendu->loadPermClass());
$smarty->assign("nb_modeles_etiquettes", $nb_modeles_etiquettes);

$smarty->display("inc_list_operations.tpl");

?>