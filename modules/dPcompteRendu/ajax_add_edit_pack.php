<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();
$user_id    = CValue::getOrSession("filter_user_id", CAppUI::$user->_id);
$pack_id    = CValue::get("pack_id");

// Pour la création d'un pack, on affecte comme utilisateur celui de la session par défaut.
$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : CAppUI::$user->_id);
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

// Chargement du pack
$where = array();
$orderby = "object_class, nom";

$pack = new CPack;

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
}
foreach($packsFunc as $_pack) {
  $_pack->loadRefsFwd();
}  
foreach($packsEtab as $_pack) {
  $_pack->loadRefsFwd();
}
// Récupération des modèles
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
$order = "nom";

// Chargement du pack
$pack->load($pack_id);

$object_guid = '';

if($pack->_id) {
  $pack->loadRefsFwd();
  $pack->loadBackRefs("modele_links");
  if ($pack->chir_id) {
    $user = new CMediUsers;
    $user->load($pack->chir_id);
    $object_guid = $user->_guid;
  } else if ($pack->function_id) {
  	$function = new CFunctions;
  	$function->load($pack->function_id);
  	$object_guid = $function->_guid;
  } else {
  	$group = new CGroups;
  	$group->load($pack->group_id);
  	$object_guid = $group->_guid;
  }
} else {
  $pack->chir_id = $userSel->_id;
  
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pack", $pack);
$smarty->assign("user_id"     , $userSel->_id);
$smarty->assign("object_guid" , $object_guid);
$smarty->assign("listUser"    , $listUser);
$smarty->assign("listFunc"    , $listFunc);
$smarty->assign("listEtab"    , $listEtab);

$smarty->display("inc_add_edit_pack.tpl"); 
?>