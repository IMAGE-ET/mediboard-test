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
$user = CUser::checkPassword(CUser::get()->user_username, $old_pwd, true);

// Mot de passe actuel correct
if (!$user->_id) {
  CAppUI::stepAjax("CUser-user_password-nomatch", UI_MSG_ERROR);
}

if (!$user->canChangePassword()) {
  CAppUI::stepAjax("CUser-password_change_forbidden", UI_MSG_ERROR);
}

$allow_change_password = CAppUI::conf("admin LDAP allow_change_password");
$ldap_linked = $user->isLDAPLinked();

// Si utilisateur associé au LDAP et modif de mot de passe non autorisée: ERROR
if (!$allow_change_password && $ldap_linked) {
  CAppUI::stepAjax("CUser_associate-ldap-no-password-change", UI_MSG_ERROR);
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

// Si utilisateur associé au LDAP et modif mdp autorisée
if ($ldap_linked && $allow_change_password) {
  try {
    CLDAP::changePassword($user, $old_pwd, $new_pwd1);
    CAppUI::stepAjax("CLDAP-change_password_succeeded", UI_MSG_OK);
  }
  catch(CMbException $e) {
    // Rétablissement de l'ancien mot de passe
    $user->_user_password = $old_pwd;
    if ($msg = $user->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    
    $e->stepAjax();
    CAppUI::stepAjax("CLDAP-change_password_failed", UI_MSG_ERROR);
  }
}

CAppUI::stepAjax("CUser-msg-password-updated", UI_MSG_OK);
CAppUI::$instance->weak_password = false;
CAppUI::callbackAjax($callback);

CApp::rip();
