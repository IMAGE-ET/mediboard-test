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
  CAppUI::stepAjax("CSourceLDAP_connect", UI_MSG_OK, $source_ldap->host);
  
  $source_ldap->ldap_bind($ldapconn, $ldaprdn, $ldappass, true);
  $user = $ldaprdn ? $ldaprdn : "anonymous";
  $user = $source_ldap->bind_rdn_suffix ? $ldaprdn.$source_ldap->bind_rdn_suffix : $user;
  CAppUI::stepAjax("CSourceLDAP_authenticate", UI_MSG_OK, $source_ldap->host, $user);
}
catch(CMbException $e) {
  $e->stepAjax(UI_MSG_ERROR);
}

if ($action == "search") {
  if ($attributes) {
    $attributes = preg_split("/\s*[,\n\|]\s*/", $attributes);
  }
  try {
    $results = $source_ldap->ldap_search($ldapconn, $filter, $attributes ? $attributes : array());
  }
  catch (CMbException $e) {
    $e->stepAjax(UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("CSourceLDAP_search-results", UI_MSG_OK, $filter);
  
  mbTrace($results);
}
