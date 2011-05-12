<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$object_id = CValue::get("object_id");

$mediuser = new CMediusers();
$mediuser->load($object_id);
$user     = $mediuser->_ref_user;

$ldaprdn  = CAppUI::conf("admin LDAP ldap_user");
$ldappass = CAppUI::conf("admin LDAP ldap_password");

$filter="(|(givenname=$mediuser->_user_first_name*)
          (sn=$mediuser->_user_last_name*)
          (samaccountname=$mediuser->_user_username*))";
$filter = utf8_encode($filter);

try {
  $source_ldap = CLDAP::bind($user, $ldaprdn, $ldappass);
  $results = $source_ldap->ldap_search($source_ldap->_ldapconn, $filter);
} catch(Exception $e) {
  $e->stepAjax(UI_MSG_ERROR);
}

$nb_users = $results["count"];
unset($results["count"]); 

$users = array();
foreach ($results as $key => $_result) {
  $objectguid = CLDAP::getObjectGUID($_result);;
  $users[$key]["objectguid"]      = $objectguid;
  $users[$key]["user_username"]   = CLDAP::getValue($_result, "samaccountname");
  $users[$key]["user_first_name"] = CLDAP::getValue($_result, "givenname");
  $users[$key]["user_last_name"]  = CLDAP::getValue($_result, "sn");
  $users[$key]["actif"]           = (CLDAP::getValue($_result, "useraccountcontrol") == 66048) ? 1 : 0;
  
  $id400               = new CIdSante400();
  $id400->tag          = CAppUI::conf("admin LDAP ldap_tag");
  $id400->id400        = $objectguid;
  $id400->object_class = "CUser";
  $id400->loadMatchingObject();
  $users[$key]["associate"] = $id400->_id ? $id400->object_id : null;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("users"         , $users);
$smarty->assign("mediuser"      , $mediuser);
$smarty->assign("nb_users"      , $nb_users);
$smarty->assign("givenname"     , CMbString::capitalize($mediuser->_user_first_name));
$smarty->assign("sn"            , strtoupper($mediuser->_user_last_name));
$smarty->assign("samaccountname", strtolower($mediuser->_user_username));
$smarty->display("inc_search_user_ldap.tpl");

?>