<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;
$can->needsRead();

// Utilisateur sélectionné ou utilisateur courant
$user_id = CValue::getOrSession("filter_user_id", $AppUI->user_id);
$class_name = CValue::getOrSession("filter_class");

$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : $AppUI->user_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefsFwd();

if (!$userSel->isPraticien()) {
  $userSel->load(null);
}

// Utilisateurs modifiables
$user = new CMediusers();
$listUser = $user->loadUsers(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$listEtab = array(CGroups::loadCurrent());

$where = array();
$orderby = "object_class, nom";

$pack = new CPack;

if ($class_name)
  $pack->object_class = $class_name;

$pack->chir_id = $userSel->_id;
$packsUser = $pack->loadMatchingList($orderby);
$pack->chir_id = null;

$pack->function_id = $userSel->function_id;
$packsFunc = $pack->loadMatchingList($orderby);
$pack->function_id = null;

$pack->group_id = $userSel->_ref_function->group_id;
$packsEtab = $pack->loadMatchingList($orderby);
$pack->group_id = null;

foreach($packsUser as $_pack)
  $_pack->loadRefsFwd();
  
foreach($packsFunc as $_pack)
  $_pack->loadRefsFwd();
  
foreach($packsEtab as $_pack)
  $_pack->loadRefsFwd();

// Récupération des modèles
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$order = "nom";

$modele = new CCompteRendu;

// Modèles de l'utilisateur
$modelesUser = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = $modele->_spec->ds->prepare("= %", $userSel->user_id);
  $modelesUser = $modele->loadlist($where, $order);
}

// Modèles de la fonction
$modelesFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = $modele->_spec->ds->prepare("= %", $userSel->function_id);
  $modelesFunc = $modele->loadlist($where, $order);
}

// Modèles de l'etablissement
$modelesEtab = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = $modele->_spec->ds->prepare("= %", $userSel->_ref_function->group_id);
  $modelesEtab = $modele->loadlist($where, $order);
}

// pack sélectionné
$pack_id = CValue::getOrSession("pack_id");
$pack = new CPack();
$pack->load($pack_id);
if($pack->_id) {
  $pack->loadRefsFwd();
} else {
  $pack->chir_id = $userSel->user_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("classes"       , CCompteRendu::getTemplatedClasses());

$smarty->assign("user_id"       , $user_id);
$smarty->assign("userSel"       , $userSel);
$smarty->assign("class_name"    , $class_name);

$smarty->assign("listUser"      , $listUser);
$smarty->assign("listFunc"      , $listFunc);
$smarty->assign("listEtab"      , $listEtab);

$smarty->assign("modelesUser"   , $modelesUser);
$smarty->assign("modelesFunc"   , $modelesFunc);
$smarty->assign("modelesEtab"   , $modelesEtab);

$smarty->assign("packsUser"     , $packsUser);
$smarty->assign("packsFunc"     , $packsFunc);
$smarty->assign("packsEtab"     , $packsEtab);

$smarty->assign("pack"          , $pack);
$smarty->assign("pack_id"       , $pack_id);

$smarty->display("vw_idx_packs.tpl");

?>