<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$username = trim(CValue::post('username'));
$password = trim(CValue::post('password'));

// If substitution happens when a session is locked
$is_locked = CValue::get("is_locked");
if ($is_locked) {
  $_SESSION['locked'] = false; 
}

if (!$username) {
  CAppUI::setMsg("Auth-failed-nousername", UI_MSG_ERROR);
}

// If admin: no need to  give a password
else if (($user->user_type == 1) && !CAppUI::conf("admin LDAP ldap_connection")) {
  $_REQUEST['loginas'] = $username;
  CAppUI::login();
}

else if (!$password) {
  CAppUI::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
}

else {
  $_REQUEST['loginas'] = $username;
  
  if (CAppUI::conf("admin LDAP ldap_connection")) {
    $_REQUEST['passwordas'] = $password;
    
    CAppUI::login(true);
  }
  else {
    if (!CUser::checkPassword($username, $password)) {
      CAppUI::setMsg("Auth-failed-combination", UI_MSG_ERROR);
    }
    else {
      CAppUI::login(true);
    }
  }
}

if ($msg = CAppUI::getMsg()) {
  echo $msg;
  return;
}
else {
  CAppUI::callbackAjax('UserSwitch.reload');
}
