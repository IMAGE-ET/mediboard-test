<?php 

/**
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_id       = CValue::post("user_id");
$user_password = CValue::post("user_password");
$callback      = CValue::post("callback");

$user = new CUser();
$user->load($user_id);

if ($user->_id && CUser::checkPassword($user->user_username, $user_password)) {
  CAppUI::callbackAjax($callback, $user_id);
}
else {
  CAppUI::setMsg("CUser-user_password-nomatch", UI_MSG_ERROR);
  echo CAppUI::getMsg();
}
