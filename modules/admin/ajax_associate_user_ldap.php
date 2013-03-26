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
  $user = CLDAP::searchAndMap($user, $source_ldap, $source_ldap->_ldapconn, $samaccountname, null, true);
} catch(CMbException $e) {
  $e->stepAjax(UI_MSG_ERROR);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user"       , $user);
$smarty->assign("association", $mediuser_id ? 0 : 1);
$smarty->display("inc_create_user_ldap.tpl");