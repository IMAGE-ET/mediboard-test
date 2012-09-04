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
$filter_class = CValue::getOrSession("filter_class", '');

if (!$user_id)
  $user_id = CAppUI::$user->_id;

$userSel = new CMediusers;
$userSel->load($user_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefsFwd();

$where = array();
$orderby = "object_class, nom";

$pack = new CPack;
$packs = array();

if ($filter_class)
  $pack->object_class = $filter_class;

$pack->user_id = $userSel->_id;
$packs["user"] = $pack->loadMatchingList($orderby);
$pack->user_id = null;

$pack->function_id = $userSel->function_id;
$packs["func"] = $pack->loadMatchingList($orderby);
$pack->function_id = null;

$pack->group_id = $userSel->_ref_function->group_id;
$packs["etab"] = $pack->loadMatchingList($orderby);
$pack->group_id = null;

foreach ($packs as $_packs_by_entity) {
  foreach($_packs_by_entity as $_pack) {
    $_pack->loadRefsFwd();
    $_pack->loadBackRefs("modele_links");
    $_pack->loadHeaderFooter();
  }
}

// Utilisateurs modifiables
$user = new CMediusers();
$listUser = $user->loadUsers(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"       , $userSel);
$smarty->assign("user_id"       , $user_id);
$smarty->assign("pack_id"       , $pack_id);
$smarty->assign("filter_class"  , $filter_class);
$smarty->assign("classes"       , CCompteRendu::getTemplatedClasses());

$smarty->assign("listUser"      , $listUser);

$smarty->assign("packs"     , $packs);

$smarty->display("inc_list_pack.tpl");

?>