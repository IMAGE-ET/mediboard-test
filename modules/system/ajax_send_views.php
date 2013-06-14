<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Minute courante
$time = CMbDT::time();
$minute = intval(CMbDT::transform($time, null, "%M"));
$hour   = intval(CMbDT::transform($time, null, "%H"));

// Opératue de l'envoi
$user = new CUser;
$user->user_username = CValue::get("username", CUser::get()->user_username);
$user->user_password = CValue::get("password");
if (!$user->user_password) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CViewSender-send-no-password");
} 

// Chargement des senders
$sender  = new CViewSender();
$where = array(
  "active" => "= '1'",
);

/** @var CViewSender[] $senders */
$senders = $sender->loadList($where, "name");

// Envoi de vues
foreach ($senders as $_sender) {
  $_sender->makeUrl($user);
  if (!$_sender->getActive($minute, $hour)) {
    unset($senders[$_sender->_id]);
    continue;
  }

  if ($user->user_password) {
    $_sender->makeUrl($user);
    $_sender->makeFile();
    $_sender->sendFile();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders", $senders);
$smarty->assign("time"   , $time);
$smarty->assign("user"   , $user);
$smarty->assign("minute" , $minute);
$smarty->display("inc_send_views.tpl");
