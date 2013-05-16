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
 * Temporary view access token
 */
class CViewAccessToken extends CMbObject {
  public $view_access_token_id;

  public $user_id;
  public $datetime_start;
  public $ttl_hours;
  public $first_use;
  public $params;
  public $hash;
  
  public $_ref_user;
  public $_url;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_access_token";
    $spec->key   = "view_access_token_id";
    $spec->uniques["hash"] = array("hash");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]        = "ref notNull class|CMediusers";
    $props["datetime_start"] = "dateTime notNull";
    $props["ttl_hours"]      = "num notNull min|1";
    $props["first_use"]      = "dateTime";
    $props["params"]         = "str notNull";
    $props["hash"]           = "str notNull length|40 show|0";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->loadRefUser()->_view . " - ";
    
    $params = $this->getParams();
    
    if (isset($params["m"])) {
      $module = $params["m"];
      
      $this->_view .= CAppUI::tr("module-$module-court");
      
      if (isset($params["a"])) {
        $action = $params["a"];
        $this->_view .= " - " . CAppUI::tr("mod-$module-tab-$action");
      }
    }
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("datetime_start");
    if (!$this->datetime_start) {
      $this->datetime_start = CMbDT::dateTime();
    }
    
    if (!$this->_id) {
      $this->hash = sha1("$this->user_id $this->datetime_start $this->ttl_hours $this->params");
    }
    
    return parent::store();
  }

  /**
   * Load a token by it's hash
   *
   * @param string $hash The hash
   *
   * @return CViewAccessToken
   */
  static function getByHash($hash) {
    $token = new self;
    $token->hash = $hash;
    $token->loadMatchingObject();
    return $token;
  } 
  
  /**
   * Parses the params string to an associative array
   * 
   * @return array An associative array
   */
  function getParams() {
    parse_str($this->params, $params);
    return $params;
  }
  
  /**
   * Buid the complete url requested by the token
   * 
   * @return string The url 
   */
  function getUrl() {
    return $this->_url = CAppUI::conf("base_url")."/?token=$this->hash";
  }
  
  /**
   * Get the token expiration date
   * 
   * @return datetime The datetime of expiration
   */
  function getTokenExpiration() {
    return CMbDT::dateTime("+ $this->ttl_hours HOURS", $this->datetime_start);
  }

  /**
   * Tells if the token is still valid regarding the datetime_start and the TTL
   * 
   * @return boolean True if still valid
   */
  function isValid() {
    if (!$this->_id) {
      return false;
    }
    
    $now = CMbDT::dateTime();
    return $now >= $this->datetime_start && $now <= $this->getTokenExpiration();
  }

  /**
   * Applies token's params to redirect the user
   * 
   * @return void
   */
  function applyParams() {
    // Save token expiration in the session
    CAppUI::$token_expiration = $this->getTokenExpiration();
    CValue::setSessionAbs("token_expiration", CAppUI::$token_expiration);
    
    $params = $this->getParams();

    if (isset($params["tab"]) && empty($params["a"])) {
      $params["a"] = $params["tab"];
      unset($params["tab"]);
    }

    foreach ($params as $key => $value) {
      $_GET[$key] = $value;
    }
  }

  /**
   * Marks the token as used
   *
   * @return void
   */
  function useIt(){
    $this->completeField("first_use");
    
    if ($this->_id && !$this->first_use) {
      $this->first_use = CMbDT::dateTime();
    }
    
    $this->store();
  }

  /**
   * Load the token's user
   *
   * @param bool $cache Use object cache
   *
   * @return CMediusers The user object
   */
  function loadRefUser($cache = true){
    return $this->_ref_user = $this->loadFwdRef("user_id", $cache);
  }
}
