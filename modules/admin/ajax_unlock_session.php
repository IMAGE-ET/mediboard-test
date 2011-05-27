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
  $user = CUser::get();
  
  if (!$password) {
    CAppUI::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
  }
  
  else if ($user->user_password != md5($password)) {
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