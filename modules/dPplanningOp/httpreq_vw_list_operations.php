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
$listUrgences = array();
$operation = new COperation();

if($userSel->_id){
  $operation->date = $date;
  $operation->chir_id = $userSel->_id;
  $listUrgences = $operation->loadMatchingList("date");
  foreach($listUrgences as &$curr_urg) {
    $curr_urg->loadRefsFwd();
    $curr_urg->loadRefsDocs();
    $curr_urg->_ref_sejour->loadRefsFwd();
    $curr_urg->_ref_sejour->loadRefsDocs();
  }
}

// Liste des opérations du jour sélectionné
$listDay = array();
$plageOp = new CPlageOp();
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
  
  $listDay = $plageOp->loadList($where, $order);
  $curr_plage = new CPlageOp();
  foreach ($listDay as $curr_plage) {
    $curr_plage->loadRefsFwd();
    $where = array();
    $where["plageop_id"] = "= '$curr_plage->_id'";
    if($userSel->_id && !$userSel->isAnesth()) {
      $where["chir_id"] = "= '$userSel->_id'";
    }
    $where["annulee"] = "= '0'";
    $op = new COperation;
    $curr_plage->_ref_operations = $op->loadList($where, "rank, horaire_voulu");
    foreach ($curr_plage->_ref_operations as $curr_op) {
      $curr_op->_ref_plageop = $curr_plage;
      $curr_op->loadRefsFwd();
      $curr_op->loadRefsDocs();
      $curr_op->_ref_sejour->loadRefsFwd();
      $curr_op->_ref_sejour->loadRefsDocs();
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

$where['object_class'] = " = '$object_class'";
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
$smarty->assign("listUrgences", $listUrgences);
$smarty->assign("listDay"     , $listDay);
$smarty->assign("board"       , $board);
$smarty->assign("nb_printers" , $nb_printers);
$smarty->assign("nb_modeles_etiquettes", $nb_modeles_etiquettes);

$smarty->display("inc_list_operations.tpl");

?>