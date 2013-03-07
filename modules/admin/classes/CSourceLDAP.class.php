<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage admin
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CSourceLDAP
 * Source LDAP
 */

class CSourceLDAP extends CMbObject {
  public $source_ldap_id;
  
  // DB Fields
  public $name;
  public $host;
  public $port;
  public $rootdn;
  public $bind_rdn_suffix;
  public $ldap_opt_protocol_version;
  public $ldap_opt_referrals;
  public $priority;
  public $secured;

  public $_options = array();
  public $_ldapconn;
  
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
    $props["priority"]                  = "num";
    $props["secured"]                   = "bool default|0";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_options = array(
      "LDAP_OPT_REFERRALS"        => $this->ldap_opt_referrals,
      "LDAP_OPT_PROTOCOL_VERSION" => $this->ldap_opt_protocol_version,
    ); 
  }
  
  /**
   * @return resource link_identifier 
   */
  function ldap_connect() {
    if (!function_exists("ldap_connect")) {
      throw new CMbException("CSourceLDAP_ldap-functions-not-available");
    }
    
    if (!$fp = @fsockopen($this->host, $this->port, $errno, $errstr, 2)) {
      throw new CMbException("CSourceLDAP_unreachable", $this->host);
    }
    
    fclose($fp);
    
    $host = ($this->secured ? "ldaps://" : "") . $this->host;
    $ldapconn = @ldap_connect($host, $this->port);
    if (!$ldapconn) {
      throw new CMbException("CSourceLDAP_no-connexion", $this->host);
    }
    
    foreach ($this->_options as $_option => $value) {
      ldap_set_option($ldapconn, constant($_option), $value);
    }

    return $ldapconn;
  }

  /**
   * Bind to the LDAP
   *
   * @param resource $ldapconn               [optional]
   * @param string   $ldaprdn                [optional]
   * @param string   $ldappass               [optional]
   * @param boolean  $showInvalidCredentials [optional]
   *
   * @throws CMbException
   * @return bool
   */
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
      $error = $this->get_error_message($ldapconn);
      throw new CMbException("CSourceLDAP-invalid_credentials", $error);
    }
    
    if (!$ldapbind) {
      $error = $this->get_error_message($ldapconn);
      throw new CMbException("CSourceLDAP_no-authenticate", $this->host, $ldaprdn, $error);
    }
    
    return true;
  }
  
  function get_error_message($ldapconn) {
    $error = ldap_errno($ldapconn);
    ldap_get_option($ldapconn, 0x0032, $extended_error);
    return ldap_err2str($error). " ($extended_error)";
  }
  
  /**
   * @param resource $ldapconn [optional]
   * @param string   $filter [optional]
   * @param array    $entry [optional]
   *
   * @return bool
   */
  function ldap_mod_replace($ldapconn = null, $dn = null, $entry) {
    if (!$ldapconn) {
      $ldapconn = $this->ldap_connect();
    }
    
    $ret = ldap_mod_replace($ldapconn, $dn, $entry);
    
    if (!$ret) {
      $error = $this->get_error_message($ldapconn);
      throw new CMbException("CSourceLDAP-entry_modify_error", $error);
    }
    
    return true;
  }
  
  /**
   * @param resource $ldapconn
   * @param string   $filter
   * @param array    $attributes [optional]
   * @return array
   */
  function ldap_search($ldapconn, $filter, $attributes = array(), $unbind = true) {
    $results = null;
    $ldapsearch = @ldap_search($ldapconn, $this->rootdn, $filter, $attributes);
    if ($ldapsearch) {
      $results = ldap_get_entries($ldapconn, $ldapsearch);
    }
    
    if ($unbind) {
      ldap_unbind($ldapconn);
    }
    
    return $results;
  }
  
  function get_dn($username) {
    $results = $this->ldap_search($this->_ldapconn, "(samaccountname=$username)", array(), false);
    
    if ($results["count"] > 1) {
      throw new CMbException("CSourceLDAP_too-many-results");
    }
    
    return $results[0]["dn"];
  }
  
  function start_tls(){
    ldap_start_tls($this->_ldapconn);
  }
}
