<?php

/**
 * Interface des listes de choix
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_id = CValue::getOrSession("user_id");

// Utilisateurs disponibles
$user = CMediusers::get();
$users = $user->loadUsers(PERM_EDIT);

$user = CMediusers::get($user_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_id", $user_id);
$smarty->assign("users"  , $users);

$smarty->display("vw_idx_listes.tpl");
