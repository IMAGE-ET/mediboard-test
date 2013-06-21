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

  // Arrays
  public $_query_params_get;
  public $_query_params_post;
  public $_session_data;

  // Form fields
  public $_date_min;
  public $_date_max;

  public $_link;

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
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->_query_params_get) {
      $this->_query_params_get = CMbSecurity::filterInput($this->_query_params_get);

      $this->query_params_get = json_encode($this->_query_params_get);
    }

    if ($this->_query_params_post) {
      $this->_query_params_post = CMbSecurity::filterInput($this->_query_params_post);

      $this->query_params_post = json_encode($this->_query_params_post);
    }

    if ($this->_session_data) {
      $this->_session_data = CMbSecurity::filterInput($this->_session_data);

      $this->session_data = json_encode($this->_session_data);
    }
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
    $props["_date_min"]         = "dateTime";
    $props["_date_max"]         = "dateTime";
    $props["_ref_user"]         = "ref class|CUser";
    $props["_link"]             = "str";
    $props["requestUID"]        = "str";

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
