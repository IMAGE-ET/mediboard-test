<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$mediuser_id    = CValue::get("mediuser_id");
$samaccountname = CValue::get("samaccountname");

$mediuser = new CMediusers();
$mediuser->load($mediuser_id);
$user     = $mediuser->_ref_user;
if (!$user) {
  $user = new CUser();
}

$ldaprdn  = CAppUI::conf("admin LDAP ldap_user");
$ldappass = CAppUI::conf("admin LDAP ldap_password");

$force_create = false;
if (!$mediuser->_id) {
  $force_create = true;
}

try {
  $source_ldap = CLDAP::bind($user, $ldaprdn, $ldappass);
} catch(Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
}

try {
  $user = CLDAP::searchAndMap($user, $source_ldap, $source_ldap->_ldapconn, $samaccountname, null, true);
} catch(Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
}

$association = $mediuser_id ? 0 : 1;  

echo "\n<script type=\"text/javascript\">window.ldap_user_id='$user->_id'; window.ldap_user_actif='$user->_user_actif'; window.no_association='$association'</script>";

?>