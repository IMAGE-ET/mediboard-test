<?php 

/**
 * $Id$
 *  
 * @category admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$mediuser_id = CValue::get("user_id");
$action      = CValue::get("action", "update");
$mediuser = new CMediusers();
$mediuser->load($mediuser_id);


if ($mediuser->_id) {
  $user = $mediuser->_ref_user;
  if ($user->_id && $user->isLDAPLinked()) {
    $ldap_idex = $user->loadLastId400(CAppUI::conf("admin LDAP ldap_tag"));

    if ($action == "update") {
      CLDAP::login($user, $ldap_idex->id400);
      CAppUI::stepAjax("user-updated-from-ldap");
    }
    elseif ($action == "unlink") {
      $ldap_idex->delete();
      CAppUI::stepAjax("user-unlink_from_ldap");
    }
  }
}

CApp::rip();