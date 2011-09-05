<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$old_pwd  = CValue::post("old_pwd" );
$new_pwd1 = CValue::post("new_pwd1");
$new_pwd2 = CValue::post("new_pwd2");
$callback = CValue::post("callback");

// Vérification du mot de passe actuel de l'utilisateur courant
$user = CUser::get();
$ds = CSQLDataSource::get("std");
$where = array();
$where["user_id"]       = $ds->prepare("= %", $user->_id);
$where["user_password"] = $ds->prepare("= %", md5($old_pwd));

// Mot de passe actuel correct
if (!$user->loadObject($where)) {
  CAppUI::stepAjax("CUser-user_password-nomatch", UI_MSG_ERROR);
}

// Mots de passe différents
if ($new_pwd1 != $new_pwd2) {
  CAppUI::stepAjax("CUser-user_password-nomatch", UI_MSG_ERROR);
}

// Enregistrement
$user->_user_password = $new_pwd1;
if ($msg = $user->store()) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

CAppUI::stepAjax("CUser-msg-password-updated", UI_MSG_OK);
CAppUI::$instance->weak_password = false;
CAppUI::callbackAjax($callback);

CApp::rip();
?>