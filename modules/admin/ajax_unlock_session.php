<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
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
