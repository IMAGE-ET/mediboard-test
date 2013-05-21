<?php

/**
 * Interface des packs de documents
 *
 * @category DPcompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
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
