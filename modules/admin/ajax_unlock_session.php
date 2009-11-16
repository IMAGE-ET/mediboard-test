<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI;

$password = trim(CValue::post('password'));
$lock = CValue::post('lock');

if ($lock) {
  $_SESSION['locked'] = true;
  CApp::rip();
}
else {
  $user = new CUser;
  $user->load($AppUI->user_id);
  
  if (!$password) {
    CAppUI::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
  }
  
  else if ($user->user_password != md5($password)) {
    CAppUI::setMsg("Auth-failed-combination", UI_MSG_ERROR);
  }
  
  if ($msg = CAppUI::getMsg()) {
    echo $msg;
    CApp::rip();
  }
  else {
    CAppUI::callbackAjax('Session.unlock');
    $_SESSION['locked'] = false;
  }
}