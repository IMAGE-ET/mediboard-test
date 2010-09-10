<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$user_id = CValue::getOrSession("filter_user_id", CAppUI::$user->_id);
$pack_id = CValue::get("pack_id");
$filter_class = CValue::getOrSession("filter_class");


$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : CAppUI::$user->_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefsFwd();

$where = array();
$orderby = "object_class, nom";

$pack = new CPack;

if ($filter_class)
  $pack->object_class = $filter_class;

$pack->chir_id = $userSel->_id;
$packsUser = $pack->loadMatchingList($orderby);
$pack->chir_id = null;

$pack->function_id = $userSel->function_id;
$packsFunc = $pack->loadMatchingList($orderby);
$pack->function_id = null;

$pack->group_id = $userSel->_ref_function->group_id;
$packsEtab = $pack->loadMatchingList($orderby);
$pack->group_id = null;

foreach($packsUser as $_pack) {
  $_pack->loadRefsFwd();
  $_pack->loadBackRefs("modele_links");
}
foreach($packsFunc as $_pack) {
  $_pack->loadRefsFwd();
  $_pack->loadBackRefs("modele_links");
}  
foreach($packsEtab as $_pack) {
  $_pack->loadRefsFwd();
  $_pack->loadBackRefs("modele_links");
}

// Utilisateurs modifiables
$user = new CMediusers();
$listUser = $user->loadUsers(PERM_EDIT);

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"       , $userSel);
$smarty->assign("user_id"       , $user_id);
$smarty->assign("pack_id"       , $pack_id);
$smarty->assign("filter_class"  , $filter_class);
$smarty->assign("classes"       , CCompteRendu::getTemplatedClasses());

$smarty->assign("listUser"      , $listUser);

$smarty->assign("modelesUser"   , $modelesUser);
$smarty->assign("modelesFunc"   , $modelesFunc);
$smarty->assign("modelesEtab"   , $modelesEtab);

$smarty->assign("packsUser"     , $packsUser);
$smarty->assign("packsFunc"     , $packsFunc);
$smarty->assign("packsEtab"     , $packsEtab);

$smarty->display("inc_list_pack.tpl");

?>