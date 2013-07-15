<?php

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

/**
 * Global request (PHP + SQL) slow queries
 */
class CLongRequestLog extends CMbObject {
  // Primary key
  public $long_request_log_id;

  // DB fields
  public $user_id;
  public $datetime;
  public $duration;
  public $server_addr;

  // JSON DB fields
  public $query_params_get;
  public $query_params_post;
  public $session_data;

  // Form fields
  public $_module;
  public $_action;
  public $_link;

  // Filter fields
  public $_date_min;
  public $_date_max;

  // Arrays
  public $_query_params_get;
  public $_query_params_post;
  public $_session_data;


  // Reference fields
  public $_ref_user;

  // Unique Request ID
  public $requestUID;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "long_request_log";
    $spec->key   = "long_request_log_id";
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    // GET
    if ($this->_query_params_get) {
      $this->_query_params_get = CMbSecurity::filterInput($this->_query_params_get);
      $this->query_params_get = json_encode($this->_query_params_get);
    }

    // POST
    if ($this->_query_params_post) {
      $this->_query_params_post = CMbSecurity::filterInput($this->_query_params_post);
      $this->query_params_post = json_encode($this->_query_params_post);
    }

    // SESSION
    if ($this->_session_data) {
      $this->_session_data = CMbSecurity::filterInput($this->_session_data);
      $this->session_data = json_encode($this->_session_data);
    }
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    $this->_query_params_get  = $get     = json_decode($this->query_params_get,  true);
    $this->_query_params_post = $post    = json_decode($this->query_params_post, true);
    $this->_session_data      = $session = json_decode($this->session_data,      true);

    $get  = is_array($get)  ? $get  : array();
    $post = is_array($post) ? $post : array();

    $this->_module = CValue::first(
      CMbArray::extract($get , "m"),
      CMbArray::extract($post, "m")
    );

    $this->_action = CValue::first(
      CMbArray::extract($get , "tab"),
      CMbArray::extract($get , "a"),
      CMbArray::extract($post, "dosql")
    );
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]           = "ref class|CMediusers";
    $props["datetime"]          = "dateTime notNull";
    $props["duration"]          = "float notNull";
    $props["server_addr"]       = "str notNull";
    $props["query_params_get"]  = "text show|0";
    $props["query_params_post"] = "text show|0";
    $props["session_data"]      = "text show|0";
    $props["requestUID"]        = "str";

    // Form fields
    $props["_module"]           = "str";
    $props["_action"]           = "str";
    $props["_link"]             = "str";
    $props["_query_params_get"]  = "php";
    $props["_query_params_post"] = "php";
    $props["_session_data"]      = "php";

    // Filter fields
    $props["_date_min"]         = "dateTime";
    $props["_date_max"]         = "dateTime";

    return $props;
  }

  /**
   * Generate the long request weblink
   *
   * @return void
   */
  function getLink() {
    if ($this->_query_params_get) {
      $this->_link = "?".http_build_query($this->_query_params_get, null, "&");
    }
    return;
  }

  /**
   * Load the referenced user
   *
   * @return CUser
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id");
  }
}
