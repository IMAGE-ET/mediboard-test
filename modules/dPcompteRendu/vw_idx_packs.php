<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$user_id      = CValue::getOrSession("user_id", CAppUI::$user->_id);
$object_class = CValue::getOrSession("filter_class");

$user = CMediusers::get($user_id);
$users   = $user->loadUsers(PERM_EDIT);
$classes = CCompteRendu::getTemplatedClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("classes"     , $classes);
$smarty->assign("users"       , $users);
$smarty->assign("user_id"     , $user_id);
$smarty->assign("object_class", $object_class);

$smarty->display("vw_idx_packs.tpl");

?>