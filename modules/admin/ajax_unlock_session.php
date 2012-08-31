<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$password = trim(CValue::post('password'));
$lock = CValue::post('lock');

if ($lock) {
  $_SESSION['locked'] = true;
  return;
}
else {
  if (!$password) {
    CAppUI::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
  }
  
  if (!CUser::checkPassword(CUser::get()->user_username, $password)) {
    CAppUI::setMsg("Auth-failed-combination", UI_MSG_ERROR);
  }
  
  if ($msg = CAppUI::getMsg()) {
    echo $msg;
    return;
  }
  else {
    CAppUI::callbackAjax('Session.unlock');
    $_SESSION['locked'] = false;
  }
}
