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
$board     = CValue::get("board", 0);
$boardItem = CValue::get("boardItem", 0);

// Urgences du jour
$list_urgences = array();
$operation = new COperation();

if($userSel->_id){
  $operation->date = $date;
  $operation->chir_id = $userSel->_id;
  $list_urgences = $operation->loadMatchingList("date");
  foreach($list_urgences as $curr_urg) {
    $curr_urg->loadRefsFwd();
    $curr_urg->loadRefsDocs();
    $curr_urg->_ref_sejour->loadRefsFwd();
    $curr_urg->_ref_sejour->loadRefsDocs();
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
    $_plage->loadRefsFwd();
    
    $where = array();
    if($userSel->_id && !$userSel->isAnesth()) {
      $where["chir_id"] = "= '$userSel->_id'";
    }
    
    $_plage->loadRefsOperations(false, "rank, rank_voulu, horaire_voulu", true, null, $where);
    
    foreach ($_plage->_ref_operations as $_op) {
      $_op->loadRefsFwd();
      $_op->loadRefsDocs();
      $_op->_ref_sejour->loadRefsFwd();
      $_op->_ref_sejour->loadRefsDocs();
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

// Modèles du praticien
$modelesByOwner = array(
  'COperation' => array(),
  'CSejour' => array()
);
$packsByOwner = array(
  'COperation' => array(),
  'CSejour' => array()
);
if ($praticien->_can->edit) {
  foreach($modelesByOwner as $object_class => $modeles) {
    $modelesByOwner[$object_class] = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $object_class, "body");
    $packsByOwner[$object_class] = CPack::loadAllPacksFor($praticien->_id, 'user', $object_class);
  }
}

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modelesByOwner", $modelesByOwner);
$smarty->assign("packsByOwner"  , $packsByOwner);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("boardItem"   , $boardItem);
$smarty->assign("date"        , $date);
$smarty->assign("listUrgences", $list_urgences);
$smarty->assign("listDay"     , $list_plages);
$smarty->assign("board"       , $board);
$smarty->assign("nb_printers" , $nb_printers);
$smarty->assign("nb_modeles_etiquettes", $nb_modeles_etiquettes);

$smarty->display("inc_list_operations.tpl");

?>