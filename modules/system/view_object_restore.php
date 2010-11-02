<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$log = new CUserLog;

$log->object_class = CValue::postOrSession("object_class");
$log->date         = CValue::postOrSession("date");
$log->user_id      = CValue::postOrSession("user_id");
$do_it             = CValue::post("do_it");

$user = new CMediusers;
$users = $user->loadGroupList();

foreach($users as $_user) {
  $_user->loadRefFunction();
}

$classes = Capp::getInstalledClasses();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("log", $log);
$smarty->assign("users", $users);
$smarty->assign("classes", $classes);
$smarty->assign("do_it", $do_it);
$smarty->display("view_object_restore.tpl");
