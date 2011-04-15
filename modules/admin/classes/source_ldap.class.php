<?php

/**
 * Source LDAP Admin
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSourceLDAP
 * Source LDAP
 */

class CSourceLDAP extends CMbObject{
  // DB Table key
  var $source_ldap_id            = null;
  
  // DB Fields
  var $name                      = null;
  var $host                      = null;
  var $port                      = null;
  var $rootdn                    = null;
  var $bind_rdn_suffix           = null;
  var $ldap_opt_protocol_version = null;
  var $ldap_opt_referrals        = null;
  
  var $_options                  = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'source_ldap';
    $spec->key   = 'source_ldap_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["name"]                      = "str notNull";
    $props["host"]                      = "text notNull";
    $props["port"]                      = "num default|389";
    $props["rootdn"]                    = "str notNull";
    $props["bind_rdn_suffix"]           = "str";
    $props["ldap_opt_protocol_version"] = "num default|3";
    $props["ldap_opt_referrals"]        = "bool default|0";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_options = array(
      "LDAP_OPT_REFERRALS"        => $this->ldap_opt_referrals,
      "LDAP_OPT_PROTOCOL_VERSION" => $this->ldap_opt_protocol_version,
    ); 
  }
  
  function ldap_connect() {
    if (!function_exists("ldap_connect")){
      throw new CMbException("CSourceLDAP_ldap-functions-not-available");
    }
    
    if (!$fp = @fsockopen($this->host, $this->port, $errno, $errstr, 2)){
      throw new CMbException("CSourceLDAP_unreachable", $this->host);
    }
    fclose($fp);
    
    $ldapconn = @ldap_connect($this->host, $this->port);
    if (!$ldapconn) {
      throw new CMbException("CSourceLDAP_no-connexion", $this->host);
    }
    
    foreach ($this->_options as $_option => $value) {
      ldap_set_option($ldapconn, constant($_option), $value);
    }

    return $ldapconn;
  }
  
  function ldap_bind($ldapconn = null, $ldaprdn = null, $ldappass = null, $showInvalidCredentials = false) {
    if (!$ldapconn) {
      $ldapconn = $this->ldap_connect();
    }
    
    if ($this->bind_rdn_suffix) {
      $ldaprdn = $ldaprdn.$this->bind_rdn_suffix;
    }
    
    $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    $error = ldap_errno($ldapconn);
    if (!$showInvalidCredentials && ($error == 49)) {
      return false;     
    }
    if (!$ldapbind) {
      throw new CMbException("CSourceLDAP_no-authenticate", $this->host, $ldaprdn, ldap_err2str($error));
    }
    
    return true;
  }
  
  function ldap_search($ldapconn, $filter, $attributes = array()) {
    $ldapsearch = ldap_search($ldapconn, $this->rootdn, $filter, $attributes);
    $results = ldap_get_entries($ldapconn, $ldapsearch);
    
    ldap_unbind($ldapconn);
    
    return $results;
  }
}
?>