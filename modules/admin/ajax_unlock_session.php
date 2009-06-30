<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI;

$password = trim(mbGetValueFromPost('password'));
$lock = mbGetValueFromPost('lock');

if ($lock) {
  $_SESSION['locked'] = true;
  CApp::rip();
}
else {
  if (!$password) {
    $AppUI->setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
  }
  
  else if ($AppUI->_ref_user->_user_password != md5($password)) {
    $AppUI->setMsg("Auth-failed-combination", UI_MSG_ERROR);
  }
  
  if ($msg = $AppUI->getMsg()) {
    echo $msg;
    CApp::rip();
  }
  else {
    CAppUI::callbackAjax('Session.unlock');
    $_SESSION['locked'] = false;
  }
}