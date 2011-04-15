<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$action         = CValue::get("action");
$source_ldap_id = CValue::get("source_ldap_id");
$ldaprdn        = CValue::get("ldaprdn");
$ldappass       = CValue::get("ldappass");
$filter         = CValue::get("filter", "(samaccountname=*)");
$attributes     = CValue::get("attributes");

$source_ldap = new CSourceLDAP();
$source_ldap->load($source_ldap_id);

try {
  $ldapconn = $source_ldap->ldap_connect();
} catch(Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
}
CAppUI::stepAjax("CSourceLDAP_connect", UI_MSG_OK, $source_ldap->host);

try {
  $source_ldap->ldap_bind($ldapconn, $ldaprdn, $ldappass, true);
} catch(Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
}
CAppUI::stepAjax("CSourceLDAP_authenticate", UI_MSG_OK, $source_ldap->host, $ldaprdn ? $ldaprdn : "anonymous");

if ($action == "search") {
  if ($attributes) {
    $attributes = preg_split("/\s*[,\n\|]\s*/", $attributes);
  }
  try {
    $results = $source_ldap->ldap_search($ldapconn, $filter, $attributes ? $attributes : array());
  } catch(Exception $e) {
    CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("CSourceLDAP_search-results", UI_MSG_OK, $filter);
  
  mbTrace($results);
}


?>