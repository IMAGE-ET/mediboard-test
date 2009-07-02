<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI;

$username = trim(mbGetValueFromPost('username'));
$password = trim(mbGetValueFromPost('password'));

if (!$username) {
  $AppUI->setMsg("Auth-failed-nousername", UI_MSG_ERROR);
}

// If admin: no need to  give a password
else if ($AppUI->user_type == 1) {
  $_REQUEST['loginas'] = $username;
  $AppUI->login();
}

else if (!$password) {
  $AppUI->setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
}

// @Todo: pass the username as a $AppUI->login argument
else {
  $_REQUEST['loginas'] = $username;
  
  $user = new CUser;
  $user->user_username = trim($username);
  $user->loadMatchingObject();
  
  if (!$AppUI->checkPasswordAttempt($user)) {
    $AppUI->setMsg("Auth-failed-loginas", UI_MSG_ERROR);
  }
  else $AppUI->login(true);
}

if ($msg = $AppUI->getMsg()) {
  echo $msg;
  CApp::rip();
}
else CAppUI::callbackAjax('location.reload');
