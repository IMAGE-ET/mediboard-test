<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$user_username   = CValue::get("user_username");
$user_first_name = CValue::get("user_first_name");
$user_last_name  = CValue::get("user_last_name");

// Création du template
$smarty = new CSmartyDP();

if ($user_username || $user_first_name || $user_last_name) {
  $ldaprdn  = CAppUI::conf("admin LDAP ldap_user");
  $ldappass = CAppUI::conf("admin LDAP ldap_password");

  try {
    $source_ldap = CLDAP::bind(null, $ldaprdn, $ldappass);
  } catch(CMbException $e) {
    $e->stepAjax(UI_MSG_ERROR);
  }
  
  $choose_filter = "";
  if ($user_username) {
    $choose_filter = "(samaccountname=$user_username*)";
  }
  if ($user_first_name) {
    $choose_filter .= "(givenname=$user_first_name*)";
  }
  if ($user_last_name) {
    $choose_filter .= "(sn=$user_last_name*)";
  }
  $filter="(|$choose_filter)";
  $filter = utf8_encode($filter);
  try {
    $results = $source_ldap->ldap_search($source_ldap->_ldapconn, $filter);
  } catch(CMbException $e) {
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

    $idex = new CIdSante400();
    $idex->tag          = CAppUI::conf("admin LDAP ldap_tag");
    $idex->id400        = $objectguid;
    $idex->object_class = "CUser";
    $idex->loadMatchingObject();
    $users[$key]["associate"] = $idex->_id ? $idex->object_id : null;
  }
  
  $mediuser = new CMediusers();

  $smarty->assign("users"         , $users);
  $smarty->assign("mediuser"      , $mediuser);
  $smarty->assign("nb_users"      , $nb_users);
  $smarty->assign("givenname"     , CMbString::capitalize($user_first_name));
  $smarty->assign("sn"            , strtoupper($user_last_name));
  $smarty->assign("samaccountname", strtolower($user_username)); 
  $smarty->display("inc_search_user_ldap.tpl");
}
else {
  $smarty->display("inc_choose_filter_ldap.tpl");
}