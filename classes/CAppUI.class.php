<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Message No Constants
define("UI_MSG_OK"     , 1);
define("UI_MSG_ALERT"  , 2);
define("UI_MSG_WARNING", 3);
define("UI_MSG_ERROR"  , 4);

/**
 * The Application UI weird Class
 * (Target) Responsibilities:
 *  - logging
 *  - messaging
 *  - localization
 *  - user preferences
 *  - system configuration
 *
 * @todo Is being split into CApp et CUI classes
 */
class CAppUI {
  /**
   * @var CAppUI
   */
  static $instance = null;

  /**
   * @var CMediusers
   */
  static $user = null;

  var $user_id = 0;

  var $_is_intranet = null;

  // DEPRECATED Use CAppUI::$user instead

  // @todo Remove all calls to these variables
  var $user_first_name = null;
  var $user_last_name = null;
  var $user_email = null;
  var $user_type = null;
  var $user_group = null;
  var $user_last_login = null;
  var $user_remote = null;

  // @todo Remove many calls in templates
  // @todo Handle the CMediusers::get() and CUser::get() cases
  var $_ref_user = null;

  // END DEPRECATED

  // Weak password
  var $weak_password = null;

  // Language alert mask
  static $locale_mask = "";

  // Language alert mask
  static $unlocalized  = array();

  static $token_expiration = null;

  // Global collections
  var $messages = array();
  var $user_prefs = array();
  var $update_hash = null;

  /**
   * @var string Default page for a redirect call
   */
  var $defaultRedirect = "";

  /**
   * @var string Session name
   */
  var $session_name = "";

  /**
   * Initializes the CAppUI singleton
   *
   * @return CAppUI The singleton
   */
  static function init() {
    return self::$instance = new CAppUI;
  }

  /**
   * Executed prior to any serialization of the object
   *
   * @return array Array of field names to be serialized
   */
  function __sleep() {
    unset($this->_ref_user);
    return array_keys(get_object_vars($this));
  }

  /**
   * Used to include a php class file from the lib directory
   * TODO Migrate to CApp
   *
   * @param string $name The class root file name (excluding .php)
   * @param bool   $rip  Trigger CApp::rip
   *
   * @return mixed Job-done bool or file return value
   */
  static function requireLibraryFile($name, $rip = true) {
    if ($root = self::conf("root_dir")) {
      $file = "$root/lib/$name.php";
      if (is_file($file)) {
        return include_once $file;
      }

      $library = ucwords(dirname($name));
      self::setMsg("La librairie <strong>$library</strong> n'est pas installée", UI_MSG_ERROR);
      if ($rip) {
        CApp::rip();
      }
    }
  }

  /**
   * Used to include a php file from the module directory
   *
   * @param string $name [optional] The module name
   * @param string $file [optional] The name of the file to include
   *
   * @return mixed Job-done bool or file return value
   * @todo Migrate to CApp
   */
  static function requireModuleFile($name = null, $file = null) {
    if ($name && $root = self::conf("root_dir")) {
      $filename = $file ? $file : $name;
      return include_once "$root/modules/$name/$filename.php";
    }
  }

  /**
   * Used to store information in tmp directory
   *
   * @param string $subpath in tmp directory
   *
   * @return string The path to the include file
   * @todo Migrate to CApp
   */
  static function getTmpPath($subpath) {
    if ($subpath && $root = self::conf("root_dir")) {
      return "$root/tmp/$subpath";
    }
  }

  /**
   * Find directories in a root subpath, excluding source control files
   *
   * @param string $subpath The subpath to read
   *
   * @return array A named array of the directories (the key and value are identical)
   */
  static function readDirs($subpath) {
    $root_dir = self::conf("root_dir");
    $dirs = array();
    $d = dir("$root_dir/$subpath");
    while (false !== ($name = $d->read())) {
      if (is_dir("$root_dir/$subpath/$name") &&
          $name !== "." &&
          $name !== ".." &&
          $name !== "CVS" &&
          $name !== ".svn"
      ) {
        $dirs[$name] = $name;
      }
    }

    $d->close();
    return $dirs;
  }

  /**
   * Find files in a roo subpath, excluding a specific filter
   *
   * @param string $subpath The path to read
   * @param string $filter  Filter as a regular expression
   *
   * @return array A named array of the files (the key and value are identical)
   */
  static function readFiles($subpath, $filter = ".") {
    $files = array();

    if ($handle = opendir($subpath)) {
      while (false !== ($file = readdir($handle))) {
        if ($file !== "." &&
            $file !== ".." &&
            preg_match("/$filter/", $file)
        ) {
          $files[$file] = $file;
        }
      }
      closedir($handle);
    }

    return $files;
  }

  /**
   * Utility function to check whether a file name is "safe"
   * Prevents from access to relative directories (eg ../../deadlyfile.php)
   *
   * @param string $file The file name
   *
   * @return string Sanitized file name
   */
  static function checkFileName($file) {
    // define bad characters and their replacement
    $bad_chars = ";.\\";
    $bad_replace = "..."; // Needs the same number of chars as $bad_chars

    // check whether the filename contained bad characters
    if (strpos(strtr($file, $bad_chars, $bad_replace), ".") !== false) {
      self::redirect("m=system&a=access_denied");
    }

    return $file;
  }

  /**
   * Redirects the browser to a new page.
   *
   * @param string $params HTTP GET paramaters to apply
   *
   * @return void
   */
  static function redirect($params = "") {

    session_write_close();

    if (!CValue::get("dontRedirect")) {
      if (CValue::get("dialog")) {
        $params .= "&dialog=1";
      }

      if (CValue::get("ajax")) {
        $params .= "&ajax=1";
      }

      if (CValue::get("suppressHeaders")) {
        $params .= "&suppressHeaders=1";
      }

      $query = ($params && $params[0] !== "#" ? "?$params" : "");
      header("Location: index.php$query");
      CApp::rip();
    }
  }

  /**
   * Returns the CSS class corresponding to a message type
   *
   * @param int $type Message type as a UI constant
   *
   * @return string The CSS class
   */
  static function getErrorClass($type = UI_MSG_OK) {
    switch ($type) {
      case UI_MSG_ERROR:
        return "error";

      case UI_MSG_WARNING:
        return "warning";

      default:
      case UI_MSG_OK:
      case UI_MSG_ALERT:
        return "info";
    }
  }

  /**
   * Add message to the the system UI
   *
   * @param string $msg  The internationalized message
   * @param int    $type [optional] Message type as a UI constant
   * @param mixed  $_    [optional] Any number of printf-like parameters to be applied
   *
   * @return void
   * @todo rename to addMsg()
   */
  static function setMsg($msg, $type = UI_MSG_OK, $_ = null) {
    $args = func_get_args();
    $msg = CAppUI::tr($msg, array_slice($args, 2));
    @self::$instance->messages[$type][$msg]++;
  }

  /**
   * Add message to the the system UI from Ajax call
   *
   * @param string $msg  The internationalized message
   * @param int    $type [optional] Message type as a UI constant
   * @param mixed  $_    [optional] Any number of printf-like parameters to be applied
   *
   * @return void
   * @todo rename to addAjaxMsg()
   */
  static function displayAjaxMsg($msg, $type = UI_MSG_OK, $_ = null) {
    $args = func_get_args();
    $msg = CAppUI::tr($msg, array_slice($args, 2));
    $msg = htmlentities($msg);
    $class = self::getErrorClass($type);
    self::callbackAjax('$("systemMsg").show().insert', "<div class='$class'>$msg</div>");
  }

  /**
   * Check whether UI has any problem message
   *
   * @return bool True if no alert/warning/error message
   */
  static function isMsgOK() {
    $messages = self::$instance->messages;
    $errors =
      count(@$messages[UI_MSG_ALERT  ]) +
      count(@$messages[UI_MSG_WARNING]) +
      count(@$messages[UI_MSG_ERROR  ]);
    return $errors == 0;
  }

  /**
   * Add a action pair message
   * Make an error is message is not null, ok otherwise
   *
   * @param string $msg    The internationalized message
   * @param string $action The internationalized action
   * @param mixed  $_      [optional] Any number of printf-like parameters to be applied to action
   *
   * @return void
   * @todo rename to addActionMsg()
   */
  static function displayMsg($msg, $action, $_ = null) {
    $args = func_get_args();
    $action = self::tr($action, array_slice($args, 2));
    if ($msg) {
      $msg = self::tr($msg);
      // @todo Should probably not translate once again
      self::setMsg("$action: $msg", UI_MSG_ERROR);
      return;
    }

    self::setMsg($action, UI_MSG_OK);
  }

  /**
   * Render HTML system message bloc corresponding to current messages
   * Possibly clear messages, thus being shown only once
   *
   * @param boolean $reset [optional] Clear messages if true
   *
   * @return string HTML divs
   */
  static function getMsg($reset = true) {
    $return = "";

    ksort(self::$instance->messages);

    foreach (self::$instance->messages as $type => $messages) {
      $class = self::getErrorClass($type);

      foreach ($messages as $message => $count) {
        $render = $count > 1 ? "$message x $count" : $message;
        $return .= "<div class='$class'>$render</div>";
      }
    }

    if ($reset) {
      self::$instance->messages = array();
    }

    return $return;
  }

  /**
   * Display an AJAX message step after translation
   *
   * @param int    $type [optional] Message type as a UI constant
   * @param string $msg  The internationalized message
   * @param mixed  $_    [optional] Any number of printf-like parameters to be applied
   *
   * @return void
   * @todo Rename to ajaxNotice()
   */
  static function stepMessage($type, $msg, $_ = null) {
    $args = func_get_args();
    $msg = CAppUI::tr($msg, array_slice($args, 2));

    $class = self::getErrorClass($type);
    echo "\n<div class='small-$class'>$msg</div>";
  }

  /**
   * Display an AJAX step, and exit on error messages
   *
   * @param string $msg  The internationalized message
   * @param int    $type [optional] Message type as a UI constant
   * @param mixed  $_    [optional] Any number of printf-like parameters to be applied
   *
   * @return void
   * @todo Switch parameter order, like stepMessage()
   * @todo Rename to ajaxNsg()
   */
  static function stepAjax($msg, $type = UI_MSG_OK, $_ = null) {
    $args = func_get_args();
    $msg = CAppUI::tr($msg, array_slice($args, 2));

    $class = self::getErrorClass($type);
    echo "\n<div class='$class'>$msg</div>";

    if ($type == UI_MSG_ERROR) {
      CApp::rip();
    }
  }

  /**
   * Echo an ajax callback with given value
   *
   * @param string $callback Name of the javascript function
   * @param string $args     Value parameter(s) for javascript function
   *
   * @return void
   */
  static function callbackAjax($callback, $args = '') {
    $args = func_get_args();
    $args = array_slice($args, 1);

    // JSON encode args
    foreach ($args as $key => $_arg) {
      if (is_array($_arg)) {
        $_arg = array_map_recursive("utf8_encode", $_arg);
      }

      if (!is_numeric($_arg)) {
        $args[$key] = json_encode($_arg);
      }
    }

    $args = implode(",", $args);
    self::js("$callback($args);");
  }

  /**
   * Echo an HTML javascript block
   *
   * @param string $script Javascript code
   *
   * @return void
   */
  static function js($script) {
    echo "\n<script>$script</script>";
  }

  /**
   * Login function, handling standard login, loginas, LDAP connection
   * Preferences get loaded on success
   *
   * @param bool $force_login To allow admin users to login as someone else
   *
   * @return boolean Job done
   */
  static function login($force_login = false) {
    $ldap_connection = CAppUI::conf("admin LDAP ldap_connection");

    // Login as
    $loginas    = trim(CValue::request("loginas"));
    $passwordas = trim(CValue::request("passwordas"));

    // LDAP
    $ldap_guid  = trim(CValue::get("ldap_guid"));

    // Standard login
    $username   = trim(CValue::request("username"));
    $password   = trim(CValue::request("password"));

    // Token sign-in
    $token_hash = trim(CValue::request("token"));

    // Test login and password validity
    $user = new CUser;
    $user->_is_logging = true;

    // -------------- Login as: no need to provide a password for administrators
    if ($loginas) {
      if (self::$instance->user_type != 1 && !$force_login) {
        self::setMsg("Auth-failed-loginas-admin", UI_MSG_ERROR);
        return false;
      }
      $username = $loginas;
      $password = ($ldap_connection ? $passwordas : null);

      $user->user_username  = $username;
      $user->_user_password = $password;
    }

    // -------------- LDAP sign-in
    elseif ($ldap_connection && $ldap_guid) {
      try {
        $user = CLDAP::getFromLDAPGuid($ldap_guid);
      }
      catch (Exception $e) {
        self::setMsg($e->getMessage(), UI_MSG_ERROR);
        return false;
      }
    }

    // -------------- Token sign-in
    elseif ($token_hash) {
      $token = CViewAccessToken::getByHash($token_hash);

      if (!$token->isValid()) {
        self::setMsg("Auth-failed-invalidToken", UI_MSG_ERROR);
        return false;
      }

      $token->useIt();
      $token->applyParams();

      $user->load($token->user_id);
    }

    // -------------- Standard sign-in
    else {
      if (!$username) {
        self::setMsg("Auth-failed-nousername", UI_MSG_ERROR);
        return false;
      }

      if (!$password) {
        self::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
        return false;
      }

      $user->user_username  = $username;
      $user->_user_password = $password;

      self::$instance->weak_password = self::checkPasswordWeakness($user);
    }

    if (!$user->_id) {
      $user->loadMatchingObject();
    }

    // User template case
    if ($user->template) {
      self::setMsg("Auth-failed-template", UI_MSG_ERROR);
      return false;
    }

    // LDAP case (when not using a ldap_guid), we check is the user in the LDAP directory is still allowed
    // TODO we shoud check it when using ldap_guid too
    if ($ldap_connection && $username) {
      $user_ldap = new CUser();
      $user_ldap->user_username = $username;
      $user_ldap->loadMatchingObject();
      $user_ldap->loadLastId400(CAppUI::conf("admin LDAP ldap_tag"));

      if ($user_ldap->_ref_last_id400->_id) {
        $user_ldap->_user_password = $password;
        $user_ldap->_bound = false;

        try {
          $user_ldap = CLDAP::login($user_ldap, $ldap_guid);

          if (!$user_ldap->_bound) {
            self::setMsg("Auth-failed-combination", UI_MSG_ERROR);
            return false;
          }
        }
        catch (CMbException $e) {
          // Maybe source unreachable ?
          // No UI_MSG_ERROR nor $e->stepAjax as it needs to run through!
          self::setMsg($e->getMessage(), UI_MSG_WARNING);
        }
      }
    }

    if (!self::checkPasswordAttempt($user)) {
      return false;
    }

    // Put user_group in AppUI
    self::$instance->user_remote = 1;

    $ds = CSQLDataSource::get("std");

    // We get the user's group if the Mediusers module is installed
    if ($ds->loadTable("users_mediboard") && $ds->loadTable("groups_mediboard")) {
      $sql = "SELECT `remote` FROM `users_mediboard` WHERE `user_id` = '$user->_id'";
      self::$instance->user_remote = $ds->loadResult($sql);

      $sql = "SELECT `groups_mediboard`.`group_id`
        FROM `groups_mediboard`, `functions_mediboard`, `users_mediboard`
        WHERE `groups_mediboard`.`group_id` = `functions_mediboard`.`group_id`
        AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`
        AND `users_mediboard`.`user_id` = '$user->_id'";
      self::$instance->user_group = $ds->loadResult($sql);
    }

    // Test if remote connection is allowed
    self::$instance->_is_intranet =
      is_intranet_ip($_SERVER["REMOTE_ADDR"]) &&
      $_SERVER["REMOTE_ADDR"] != self::conf("system reverse_proxy");

    if (!self::$instance->_is_intranet && self::$instance->user_remote == 1 && $user->user_type != 1) {
      self::setMsg("Auth-failed-user-noremoteaccess", UI_MSG_ERROR);
      return false;
    }

    self::$instance->user_id = $user->_id;

    // <DEPRECATED>
    self::$instance->user_first_name = $user->user_first_name;
    self::$instance->user_last_name  = $user->user_last_name;
    self::$instance->user_email      = $user->user_email;
    self::$instance->user_type       = $user->user_type;
    self::$instance->user_last_login = $user->user_last_login;
    // </DEPRECATED>

    // save the last_login dateTime
    if ($ds->loadField("users", "user_last_login")) {
      // Nullify password or you hash it once more
      $user->user_last_name = null;
      $user->user_last_login = mbDateTime();
      $user->store();
    }

    // load the user preferences
    self::buildPrefs();

    return true;
  }

  /**
   * Check password weakness
   *
   * @param CUser $user User whose password to check
   *
   * @return bool True if password is too weak
   */
  static function checkPasswordWeakness(CUser $user) {
    if (null == $pwd = $user->_user_password) {
      return false;
    }

    $pwdSpecs = $user->_specs['_user_password'];

    // minLength
    if ($pwdSpecs->minLength > strlen($pwd)) {
      return true;
    }

    // notContaining
    if ($pwdSpecs->notContaining) {
      $target = $pwdSpecs->notContaining;
      if (($field = $user->$target) && stristr($pwd, $field)) {
        return true;
      }
    }

    // notNear
    if ($pwdSpecs->notNear) {
      $target = $pwdSpecs->notNear;
      if (($field = $user->$target) && (levenshtein($pwd, $field) < 3)) {
        return true;
      }
    }

    // alphaAndNum
    if ($pwdSpecs->alphaAndNum) {
      if (!preg_match("/[a-z]/i", $pwd) || !preg_match("/\d+/", $pwd)) {
        return true;
      }
    }
  }

  /**
   * Check wether login/password is found
   * Handle password attempts count
   *
   * @param CUser $user User whose password attempt to check
   *
   * @return bool True is attempt is successful
   */
  static function checkPasswordAttempt(CUser $user) {
    $sibling = new CUser;
    $sibling->user_username = $user->user_username;
    $sibling->loadMatchingObject();
    $sibling->loadRefMediuser();

    $mediuser = $sibling->_ref_mediuser;

    if ($mediuser && $mediuser->_id) {
      if (!$mediuser->actif) {
        self::setMsg("Auth-failed-user-deactivated", UI_MSG_ERROR);
        return false;
      }

      $today = mbDate();
      $deb = $mediuser->deb_activite;
      $fin = $mediuser->fin_activite;

      // Check if the user is in his activity period
      if ($deb && $deb > $today || $fin && $fin <= $today) {
        self::setMsg("Auth-failed-user-deactivated", UI_MSG_ERROR);
        return false;
      }
    }

    if ($sibling->_login_locked) {
      self::setMsg("Auth-failed-user-locked", UI_MSG_ERROR);
      return false;
    }

    // Wrong login and/or password
    if (!$user->_id) {
      self::setMsg("Auth-failed-combination", UI_MSG_ERROR);

      // If the user exists, but has given a wrong password let's increment his error count
      if ($user->loginErrorsReady() && $sibling->_id) {
        $sibling->user_login_errors++;
        $sibling->store();
        $remainingAttempts = max(0, self::conf("admin CUser max_login_attempts")-$sibling->user_login_errors);
        self::setMsg("Auth-failed-tried", UI_MSG_ERROR, $sibling->user_login_errors, $remainingAttempts);
      }
      return false;
    }

    // Logging succesfull
    $user->user_login_errors = 0;
    $user->store();
    return true;
  }

  /**
   * Load the stored user preferences from the database into cache
   *
   * @param integer $user_id User ID, 0 for default preferences
   *
   * @return void
   */
  static function loadPrefs($user_id = null) {
    // Former pure SQL system
    $ds = CSQLDataSource::get("std");
    if ($ds->loadField("user_preferences", "pref_name")) {
      $query = "SELECT pref_name, pref_value
        FROM user_preferences
        WHERE pref_user = '$user_id'";
      $user_prefs = $ds->loadHashList($query);
    }
    // Latter object oriented system
    else {
      $user_prefs = CPreferences::get($user_id);
    }

    self::$instance->user_prefs = array_merge(self::$instance->user_prefs, $user_prefs);
  }

  /**
   * Build preferences for connected user, with the default/profile/user strategy
   *
   * @return void
   */
  static function buildPrefs() {
    // Default
    self::loadPrefs();

    // Profile
    $user = CUser::get();
    if ($user->profile_id) {
      self::loadPrefs($user->profile_id);
    }

    // User
    self::loadPrefs($user->_id);
  }


  /**
   * Get a named user preference value
   *
   * @param string $name    Name of the user preference
   * @param string $default [optional] A default value when preference is not set
   *
   * @return string The value
   */
  static function pref($name, $default = null) {
    $prefs = self::$instance->user_prefs;
    return isset($prefs[$name]) ? $prefs[$name] : $default;
  }

  /**
   * Returns the list of $locale files
   *
   * @param string $locale The locale name of the paths
   *
   * @return array The paths
   */
  static function getLocaleFilesPaths($locale) {
    global $root_dir;

    $paths = array_merge(
      glob("$root_dir/locales/$locale/*.php"),
      glob("$root_dir/modules/*/locales/$locale.php")
    );

    return $paths;
  }

  /**
   * Check translated statement exists
   *
   * @param string $str statement to translate
   *
   * @return boolean if translated statement exists
   */
  static function isTranslated($str) {
    global $locales;
    return array_key_exists($str, $locales);
  }

  /**
   * Localization skipped if false
   * @var boolean
   */
  static $localize = true;

  /**
   * Mask of strings to ignore for the localization warning
   * @var string
   */
  static $localize_ignore = '/(^CExObject|^CMbObject\.dummy)/';

  /**
   * Localize given statement
   *
   * @param string $str  Statement to translate
   * @param array  $args Array or any number of sprintf-like arguments
   *
   * @return string translated statement
   */
  static function tr($str, $args = null) {
    global $locales;

    $str = trim($str);
    if (empty($str)) {
      return "";
    }

    // Defined and not empty
    if (isset($locales) && self::$localize) {
      if (isset($locales[$str]) && $locales[$str] !== "") {
        $str = $locales[$str];
      }
      // Other wise keep it in a stack...
      else {
        if (!in_array($str, self::$unlocalized) && !preg_match(self::$localize_ignore, $str)) {
          self::$unlocalized[] = $str;
        }
        // ... and decorate
        if (self::$locale_mask) {
          $str = sprintf(self::$locale_mask, $str);
        }
      }
    }

    if ($args !== null) {
      if (!is_array($args)) {
        $args = func_get_args();
        unset($args[0]);
      }
      $str = vsprintf($str, $args);
    }


    return nl2br($str);
  }

  /**
   * Return the configuration setting for a given path
   *
   * @param string $path    Tokenized path, eg "module class var", dP proof
   * @param mixed  $context The context
   *
   * @return mixed String or array of values depending on the path
   */
  static function conf($path = "", $context = null) {
    if ($context) {
      if ($context instanceof CMbObject) {
        $context = $context->_guid;
      }

      return CConfiguration::getValue($context, $path);
    }

    global $dPconfig;
    $conf = $dPconfig;
    if (!$path) {
      return $conf;
    }

    $items = explode(' ', $path);
    foreach ($items as $part) {
      // dP ugly hack
      if (!array_key_exists($part, $conf) && array_key_exists("dP$part", $conf)) {
        $part = "dP$part";
      }

      $conf = $conf[$part];
    }

    return $conf;
  }


  /**
   * @var int Global unique id
   **/
  static $unique_id = 0;

  /**
   * Produce a unique ID in the HTTP request scope
   *
   * @return integer The ID
   * @todo: $unique_id should be internal to function
   */
  static function uniqueId() {
    return self::$unique_id++;
  }

  /**
   * Produce an HTML table for given array with options
   *
   * @param array $array   Array of array of values for the table
   * @param array $options Array of options
   *
   * @return html HTML table
   * @todo TO BE REMOVED, should not exist, build a template instead
   */
  static function htmlTable($array, $options = array()) {
    $options += array(
      'tableClass' => '',
      'tableStyle' => 'width: 100%',
      'firstRowHeader' => true,
      'firstColHeader' => false,
    );

    $output = '<table class="'.$options['tableClass'].'" style="'.$options['tableStyle'].'"><tbody>';
    foreach ($array as $y => $row) {
      $output .= '<tr>';
      foreach ($row as $x => $cell) {
        $output .= ($options['firstRowHeader'] && $y == 0 ||
                    $options['firstColHeader'] && $x == 0) ? "<th>$cell</th>" : "<td>$cell</td>";
      }
      $output .= '</tr>';
    }
    return "$output</tbody></table>";
  }

  /**
   * Check if session is up to date by comparing with module versions
   *
   * @return void
   */
  static function checkSessionUpdate(){
    global $version;

    $instance = CAppUI::$instance;

    if (!$instance->user_id) {
      return;
    }

    $query = "SELECT GROUP_CONCAT(`mod_name`, `mod_version`) FROM `modules`";
    $hash  = CSQLDataSource::get("std")->loadResult($query);

    $hash .= $version["build"];

    if (!isset($instance->update_hash) || $instance->update_hash != $hash) {
      self::buildPrefs();
      $instance->update_hash = $hash;
    }
  }

  static function isTokenSessionExpired(){
    if (!CAppUI::$token_expiration) {
      return false;
    }

    return mbDateTime() >= CAppUI::$token_expiration;
  }
}

// choose to alert for missing translation or not
$locale_alert = CAppUI::conf("locale_alert");
CAppUI::$locale_mask = CAppUI::conf("locale_warn") ? "$locale_alert%s$locale_alert" : null;
