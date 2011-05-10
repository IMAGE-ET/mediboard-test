<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Minute courante
$time = mbTime();
$minute = intval(mbTransformTime($time, null, "%M"));

$user = new CUser;
$user->user_username = CValue::get("username", CUser::get()->user_username);
$user->user_password = CValue::get("password");


$senders = array();
if (!$user->user_password) {
  CAppUI::stepAjax("CViewSender-send-no-password", UI_MSG_WARNING);
} else {
  // Chargement des senders
  $sender  = new CViewSender();
  $senders = $sender->loadList(null, "name");
  
  foreach ($senders as $_sender) {
    if ($_sender->getActive($minute)) {
      $_sender->makeUrl($user);
      $_sender->makeFile();
      $_sender->sendFile();
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders", $senders);
$smarty->assign("time", $time);
$smarty->assign("user", $user);
$smarty->assign("minute", $minute);
$smarty->display("inc_send_views.tpl");
