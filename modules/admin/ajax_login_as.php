<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI;

$username = trim(CValue::post('username'));
$password = trim(CValue::post('password'));

if (!$username) {
  CAppUI::setMsg("Auth-failed-nousername", UI_MSG_ERROR);
}

// If admin: no need to  give a password
else if ($AppUI->user_type == 1) {
  $_REQUEST['loginas'] = $username;
  CAppUI::login();
}

else if (!$password) {
  CAppUI::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
}

// @Todo: pass the username as a $AppUI->login argument
else {
  $_REQUEST['loginas'] = $username;
  
  $user = new CUser;
  $user->user_username = trim($username);
  $user->loadMatchingObject();
	
  if (md5($password) != $user->user_password) {
    CAppUI::setMsg("Auth-failed-combination", UI_MSG_ERROR);
  }
  else CAppUI::login(true);
}

if ($msg = CAppUI::getMsg()) {
  echo $msg;
  return;
}
else CAppUI::callbackAjax('UserSwitch.reload');
