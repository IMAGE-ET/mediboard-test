<?php

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * User authentication
 */
class CUserAuthentication extends CMbObject {
  public $user_authentication_id;

  public $user_id;
  public $previous_user_id;
  public $auth_method;
  public $datetime_login;
  public $datetime_logout;
  public $id_address;
  public $session_id;

  // Screen
  public $screen_width;
  public $screen_height;

  // User agent
  public $user_agent_id;

  /** @var CUser */
  public $_ref_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "user_authentication";
    $spec->key    = "user_authentication_id";
    $spec->loggable = false;
    return $spec;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]          = "ref notNull class|CUser";
    $props["previous_user_id"] = "ref class|CUser";
    $props["auth_method"]      = "enum list|basic|ldap|ldap_guid|token";
    $props["datetime_login"]   = "dateTime notNull";
    $props["datetime_logout"]  = "dateTime";
    $props["id_address"]       = "str notNull";
    $props["session_id"]       = "str notNull";

    // Screen
    $props["screen_width"]     = "num";
    $props["screen_height"]    = "num";

    // User agent
    $props["user_agent_id"]    = "ref class|CUserAgent";
    return $props;
  }

  /**
   * Tells if the "user_authentication" table exists
   *
   * @return bool
   */
  static function authReady(){
    static $ready = null;

    if ($ready === null) {
      $ds = CSQLDataSource::get("std");
      $ready = $ds->loadTable("user_authentication") != null;
    }

    return $ready;
  }

  /**
   * Log user authentication
   *
   * @param CUser $user The user logging-in
   *
   * @return void
   */
  static function logAuth(CUser $user) {
    if (!self::authReady() || $user->dont_log_connection) {
      return;
    }

    global $rootName;

    $session_name = preg_replace("/[^a-z0-9]/i", "", $rootName);
    $app = CAppUI::$instance;

    $auth = new self;
    $auth->user_id = $user->_id;
    $auth->previous_user_id = null;
    $auth->auth_method = $app->auth_method;
    $auth->datetime_login = CMbDT::dateTime();
    $auth->id_address = $app->ip;
    $auth->session_id = session_id();

    // Screen size
    $cookie = CValue::cookie("$session_name-uainfo");
    $uainfo = stripslashes($cookie);

    if ($uainfo) {
      $uainfo = json_decode($uainfo, true);
      if (isset($uainfo["screen"])) {
        $screen = $uainfo["screen"];
        $auth->screen_width  = (int)$screen[0];
        $auth->screen_height = (int)$screen[1];
      }
    }

    // User agent
    $user_agent_string = CValue::read($_SERVER, "HTTP_USER_AGENT");
    if ($user_agent_string) {
      $user_agent = CUserAgent::createFromUA($user_agent_string);
      $auth->user_agent_id = $user_agent->_id;
    }

    $auth->store();
  }

  /**
   * Get user
   *
   * @return CUser
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", true);
  }
}
