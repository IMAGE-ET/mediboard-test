<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

// Récuperation de l'utilisateur sélectionné
$user_id = CValue::get("user_id");
$start   = CValue::get("start");

$user = new CUser();
$user->load($user_id);
$user->countConnections();

$user_authentication = new CUserAuthentication();
$ds = $user_authentication->getDS();

$where = array(
  "user_id" => $ds->prepare("= ?", $user_id),
);

$limit = intval($start).", 30";
$list = $user_authentication->loadList($where, "datetime_login DESC", $limit);

$smarty = new CSmartyDP();

$smarty->assign("list", $list);
$smarty->assign("user", $user);

$smarty->display("inc_vw_user_authentications.tpl");
