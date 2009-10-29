<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Message No Constants
define("UI_MSG_OK"     , 1);
define("UI_MSG_ALERT"  , 2);
define("UI_MSG_WARNING", 3);
define("UI_MSG_ERROR"  , 4);

/**
 * The true application class
 */
class CApp {
  static $inPeace = false;
  
  /**
   * Will trigger an error for logging purpose whenever the application dies unexpectely
   */
  static function checkPeace() {
    if (!self::$inPeace) {
      trigger_error("Application died unexpectedly", E_USER_ERROR);      
    }
  }
  
  /**
   * Make application die properly
   */
  static function rip() {
    self::$inPeace = true;
    die;
  }
}


/**
 * The Application UI weird Class
 * @TODO Is being split into CApp et CUI classes
 * @TODO Should be more like the singleton pattern described here : http://en.wikipedia.org/wiki/Singleton_pattern#PHP
 */
class CAppUI {
  static $instance = null;
  
  var $user_id = 0;
  
  var $_ref_user = null;
  var $_is_intranet = null;

  // DEPRECATED Use $_ref_user instead
  // @TODO Remove all calls to these variables
  var $user_first_name = null;
  var $user_last_name = null;
  var $user_email = null;
  var $user_type = null;
  var $user_group = null;
  var $user_last_login = null;
  var $user_remote = null;
  // END DEPRECATED

  /** @var bool Weak password */
  var $weak_password = null;

  /** @var string langage alert mask */
  static $locale_mask = "";
  
  /** @var int Global unique id */
  static $unique_id = 0;

  // Global collections
  var $messages = array();
  var $user_prefs = array();
  var $state = array();
  
  /** @var string Default page for a redirect call*/
  var $defaultRedirect = "";

  static function getAllClasses() {
    $rootDir = self::conf("root_dir");
    $dirs = array(
      "classes/*/*.class.php", // Require all global classes
      "classes/*.class.php", 
      "modules/*/*.class.php", // Require all modules classes
      "modules/*/setup.php" // Require all modules setups 
    );
    
    foreach ($dirs as $dir) {
      $files = glob($dir);
      foreach ($files as $fileName) {
        require_once("$rootDir/$fileName");
      }
    }
  }
  
  /**
   * Used to include a php class file from the system classes directory
   * @param string $name The class root file name (excluding .class.php)
   */
  static function requireSystemClass($name) {
    if ($root = self::conf("root_dir")) {
      return require_once("$root/classes/$name.class.php");
    }
  }

  /**
   * Used to include a php class file from the legacy classes directory
   * @param string <b>$name</b> The class file name (excluding .class.php)
   * @return string The path to the include file
   */
  static function requireLegacyClass($name) {
    if ($root = self::conf("root_dir")) {
      return require_once("$root/legacy/$name.class.php");
    }
  }
  
  /**
   * Used to include a php class file from the lib directory
   * @param string <b>$name</b> The class root file name (excluding .php)
   */
  static function requireLibraryFile($name) {
    if ($root = self::conf("root_dir")) {
      $file = "$root/lib/$name.php";
      if (is_file($file))
        return require_once($file);
      else
        self::setMsg("La librairie <b>".ucwords(dirname($name))."</b> n'est pas installée", UI_MSG_ERROR); die;
    }
  }
  
  /**
   * Used to load a php class file from the module directory
   * @param string $name The class root file name (excluding .class.php)
   */
  static function requireModuleClass($name = null, $file = null) {
    if ($name && $root = self::conf("root_dir")) {
      $filename = $file ? $file : $name;
      return require_once("$root/modules/$name/$filename.class.php");
    }
  }
  
/**
 * Used to include a php file from the module directory
 * @param string $name The class root file name (excluding .class.php)
 */
  static function requireModuleFile($name = null, $file = null) {
    if ($name && $root = self::conf("root_dir")) {
      $filename = $file ? $file : $name;
      return require_once("$root/modules/$name/$filename.php");
    }
  }
  
/**
 * Used to store information in tmp directory
 * @param string $subpath in tmp directory
 * @return string The path to the include file
 */
  static function getTmpPath($subpath) {
    if ($subpath && $root = self::conf("root_dir")) {
      return "$root/tmp/$subpath";
    }
  }
  
/**
 * Utility function to read the "directories" under "path"
 *
 * This function is used to read the modules or locales installed on the file system.
 * @param string The path to read.
 * @return array A named array of the directories (the key and value are identical).
 */
  static function readDirs($path) {
    $dirs = array();
    $d = dir(self::conf("root_dir") . "/$path");
    while (false !== ($name = $d->read())) {
      if(is_dir(self::conf("root_dir")."/$path/$name") && $name != "." && $name != ".." && $name != "CVS") {
        $dirs[$name] = $name;
      }
    }
    $d->close();
    return $dirs;
  }

/**
 * Utility function to read the "files" under "path"
 * @param string The path to read.
 * @param string A regular expression to filter by.
 * @return array A named array of the files (the key and value are identical).
 */
  static function readFiles($path, $filter=".") {
    $files = array();

    if ($handle = opendir($path)) {
      while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != ".." && preg_match("/$filter/", $file)) { 
          $files[$file] = $file; 
        } 
      }
      closedir($handle); 
    }
    return $files;
  }

/**
 * Utility function to check whether a file name is "safe"
 *
 * Prevents from access to relative directories (eg ../../dealyfile.php);
 * @param string The file name.
 * @return array A named array of the files (the key and value are identical).
 */
  static function checkFileName($file) {
    // define bad characters and their replacement
    $bad_chars = ";.\\";
    $bad_replace = "..."; // Needs the same number of chars as $bad_chars

    // check whether the filename contained bad characters
    if (strpos(strtr($file, $bad_chars, $bad_replace), ".") !== false) {
      self::redirect("m=system&a=access_denied");
    }
    else {
      return $file;
    }
  }

/**
	* Save the url query string
	*
	* Also saves one level of history.  This is useful for returning from a delete
	* operation where the record more not now exist.  Returning to a view page
	* would be a nonsense in this case.
	* @param string If not set then the current url query string is used
	*/
  static function savePlace($query = "") {
    if (!$query) {
      $query = @$_SERVER["QUERY_STRING"];
    }
    if ($query != @self::$instance->state["SAVEDPLACE"]) {
      self::$instance->state["SAVEDPLACE-1"] = @self::$instance->state["SAVEDPLACE"];
      self::$instance->state["SAVEDPLACE"] = $query;
    }
  }

/**
	* Redirects the browser to a new page.
	*
	* Mostly used in conjunction with the savePlace method. It is generally used
	* to prevent nasties from doing a browser refresh after a db update.  The
	* method deliberately does not use javascript to effect the redirect.
	*
	* @param string The URL query string to append to the URL
	* @param string A marker for a historic "place", only -1 or an empty string is valid.
	*/
  static function redirect($params="", $hist="") {
    $session_id = SID;

    session_write_close();
  
    // are the params empty
    if (!$params) {
    // has a place been saved
      $params = !empty(self::$instance->state["SAVEDPLACE$hist"]) ? self::$instance->state["SAVEDPLACE$hist"] : self::$instance->defaultRedirect;
    }
    
    if (mbGetValueFromGet("dialog")) {
      $params .= "&dialog=1";
    }
    
    if (mbGetValueFromGet("ajax")) {
      $params .= "&ajax=1";
    }
    
    if (mbGetValueFromGet("suppressHeaders")) {
      $params .= "&suppressHeaders=1";
    }
    
    // Fix to handle cookieless sessions
    if ($session_id != "") {
      if (!$params)
        $params = $session_id;
      else
        $params .= "&" . $session_id;
    }
    header("Location: index.php?$params");
    CApp::rip();
  }
  
 /**
  * Add message to the the system UI
  * @param string $msg The (translated) message
  * @param int $type type of message (cf UI constants)
  * @param any number of printf-like parameters to be applied 
  */
  static function setMsg($msg, $type = UI_MSG_OK) {
    // Formatage
    $args = func_get_args();
    $args[0] = self::tr($msg);
    unset($args[1]);
    $msg = call_user_func_array("sprintf", $args);

    // Ajout
    @self::$instance->messages[$type][$msg]++;
  }
  
  static function isMsgOK() {
    $messages = self::$instance->messages;
    $errors = count(@$messages[UI_MSG_ALERT]) + 
              count(@$messages[UI_MSG_WARNING]) +
              count(@$messages[UI_MSG_ERROR]);
    return $errors == 0;
  }
  
  /**
   * Retourne le message résultant de la modification d'un objet
   * @param string $msg résultat de la modification
   * @param string $action message à afficher
   */
  static function displayMsg($msg, $action) {
    $action = self::tr($action);
    $msg ? self::setMsg("$action: $msg", UI_MSG_ERROR ) : self::setMsg($action, UI_MSG_OK );
  }

  /**
   * Display the formatted message and icon
   * @param boolean $reset If true the system UI is cleared
   */
  static function getMsg($reset = true) {
    $return = "";
    
    ksort(self::$instance->messages);
    
    foreach (self::$instance->messages as $type => $messages) {
      switch ($type) {
        case UI_MSG_ERROR   : $class = "error" ; break;
        case UI_MSG_WARNING : $class = "warning"; break;
        default:
        case UI_MSG_OK      : 
        case UI_MSG_ALERT   : $class = "message"; break;
      }

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
  * Display an message step after translation
  * @param enum $msgType Type of message [UI_MSG_OK|UI_MSG_WARNING|UI_MSG_ERROR]
  * @param string $msg The message
  */
  static function stepMessage($msgType, $msg) {
    switch ($msgType) {
      case UI_MSG_ERROR   : $class = "small-error" ; break;
      case UI_MSG_WARNING : $class = "small-warning"; break;
      default: 
      case UI_MSG_OK      : $class = "small-message"; break;
    }
    
    $msg = nl2br(self::tr($msg));
    echo "\n<div class='$class'>$msg</div>";
  }
  
 /**
  * Display an ajax step, and exit on error messages
  * @TODO Switch parameter order, like stepMessage()
  * @param string $msg : the message
  * @param enum $msgType : type of message [UI_MSG_OK|UI_MSG_WARNING|UI_MSG_ERROR]
  */
  static function stepAjax($msg, $msgType = UI_MSG_OK) {
    switch($msgType) {
      case UI_MSG_ERROR   : $class = "error" ; break;
      case UI_MSG_WARNING : $class = "warning"; break;
      default: 
      case UI_MSG_OK      : $class = "message"; break;
    }
    
    // Formatage
    $args = func_get_args();
    unset($args[1]);
    $msg = call_user_func_array(array("CAppUI", "tr"), $args);
    $msg = nl2br($msg);

    echo "\n<div class='$class'>$msg</div>";
    
    if ($msgType == UI_MSG_ERROR) {
      CApp::rip();
    }
  }

 /**
  * Echo an ajax callback with given value
  * @param string $callback : name of the javascript function 
  * @param string $value : value paramater for javascript function
  */
  static function callbackAjax($callback, $value = '') {
    $value = json_encode($value);
    echo "\n<script type='text/javascript'>$callback($value);</script>";
  }
  
/**
 * Login function
 *
 * Upon a successful username and password match, several fields from the user
 * table are loaded in this object for convenient reference.  The style, locales
 * and preferences are also loaded at this time.
 *
 * @param string The user login name
 * @param string The user password
 * @return boolean True if successful, false if not
 */
  static function login($force_login = false) {
  	$ds = CSQLDataSource::get("std");
  	
    // Test login and password validity
    $user = new CUser;
    
    // Login as: no need to provide a password for administators
    if ($loginas = mbGetValueFromRequest("loginas")) {
      if (self::$instance->user_type != 1 && !$force_login) {
        self::setMsg("Auth-failed-loginas-admin", UI_MSG_ERROR);
        return false;
      }
      
      $user->user_username = trim($loginas);
      $user->_user_password = null;
    } 
    // Standard login
    else {
      if (null == $user->user_username  = trim(mbGetValueFromRequest("username"))) {
        self::setMsg("Auth-failed-nousername", UI_MSG_ERROR);
        return false;
      }

      if (null == $user->_user_password = trim(mbGetValueFromRequest("password"))) {
        self::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
        return false;
      }

      self::$instance->weak_password = self::checkPasswordWeakness($user);
    }
    
    // See CUser::updateDBFields
    $user->loadMatchingObject();
    
    if (!self::checkPasswordAttempt($user)) {
      return false;
    }
        
    // Put user_group in AppUI
    self::$instance->user_remote = 1;
    
    // @todo: is all this stuff necessary ?
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
    self::$instance->_is_intranet = is_intranet_ip($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] != self::conf("system reverse_proxy"));
    if (!self::$instance->_is_intranet && self::$instance->user_remote == 1 && $user->user_type != 1) {
      self::setMsg("User has no remote access", UI_MSG_ERROR);
      return false;
    }

    self::$instance->user_id = $user->_id;
    
    // DEPRECATED
    self::$instance->user_first_name = $user->user_first_name;
    self::$instance->user_last_name  = $user->user_last_name;
    self::$instance->user_email      = $user->user_email;
    self::$instance->user_type       = $user->user_type;
    self::$instance->user_last_login = $user->user_last_login;
    // END DEPRECATED
    
    // save the last_login dateTime
    if($ds->loadField("users", "user_last_login")) {
      // Nullify password or you md5 it once more
      $user->user_last_name = null;
      $user->user_last_login = mbDateTime();
      $user->store();
    }

    // load the user preferences
    self::loadPrefs(self::$instance->user_id);
    
    return true;
  }
  
  /**
   * Check password strength
   * 
   * @param CUser $user
   * @return bool
   */
  static function checkPasswordWeakness($user) {
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
        if (($field = $user->$target) && stristr($pwd, $field))
          return true;
    }
    
    // notNear
    if ($pwdSpecs->notNear) {
      $target = $pwdSpecs->notNear;
        if (($field = $user->$target) && (levenshtein($pwd, $field) < 3))
          return true;
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
   * Handler password attempts count
   *
   * @param CUser $user
   * @return bool
   */
  static function checkPasswordAttempt($user) {
    $sibling = new CUser;
    $sibling->user_username = $user->user_username;
    $sibling->loadMatchingObject();
    
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
 * Load the stored user preferences from the database into the internal
 * preferences variable.
 * @param int $uid User id number, 0 for default preferences
 */
  static function loadPrefs($uid = 0) {
    $query = "SELECT pref_name, pref_value FROM user_preferences WHERE pref_user = '$uid'";
    $user_prefs = CSQLDataSource::get("std")->loadHashList($query);
    self::$instance->user_prefs = array_merge(self::$instance->user_prefs, $user_prefs);
  }
  
  static function reloadPrefs() {
    self::loadPrefs();
    self::loadPrefs(self::$instance->user_id);
  }
  
  /**
   * Get a named user preference
   * @param string $name of the user preference
   * @return string The value
   */
  static function pref($name = null, $default = null) {
    $prefs = self::$instance->user_prefs;
    if (!$name) return $prefs;
    return isset($prefs[$name]) ? $prefs[$name] : $default; 
  }

  /**
   * Translate given statement
   * @param string $str statement to translate
   * @return string translated statement
   */
  static function tr($str) {
    global $locales;
    
    $str = trim($str);
    if (empty($str)) {
      return "";
    }
    
    // Defined and not empty
    if (!empty($locales[$str])) {
      return $locales[$str];
    }
    
    // DO NOT REMOVE THIS ASSIGNATION or else, there will be a fatal error in PHP < 5.3
    // Fatal error: func_get_args(): Can't be used as a function parameter
    $args = func_get_args();
    return nl2br(sprintf(self::$locale_mask, call_user_func_array("sprintf", $args)));
  }

  /**
   * Return the configuration setting for a given path
   * @param $path string Tokenized path, eg "module class var";
   * @return mixed scalar or array of values depending on the path
   */
  static function conf($path = '') {
    global $dPconfig;
    $conf = $dPconfig;
    if (!$path) {
      return $conf;
    }
    
    $items = explode(' ', $path);
    foreach ($items as $part) {
    	$conf = $conf[$part];
    }
    return $conf;
  }
  
  static function unique_id() {
    return self::$unique_id++;
  }
	
  static function HtmlTable($array, $options = array()) {
    $options += array(
      'tableClass' => '',
      'tableStyle' => 'width: 100%',
      'firstRowHeader' => true,
      'firstColHeader' => false,
    );
    
    $output = '<table class="'.$options['tableClass'].'" style="'.$options['tableStyle'].'"><tbody>';
    foreach($array as $y => $row) {
      $output .= '<tr>';
      foreach($row as $x => $cell) {
        $output .= ($options['firstRowHeader'] && $y == 0 || 
                    $options['firstColHeader'] && $x == 0) ? "<th>$cell</th>" : "<td>$cell</td>";
      }
      $output .= '</tr>';
    }
    return "$output</tbody></table>";
  }
}

// choose to alert for missing translation or not
$locale_warn = CAppUI::conf("locale_warn") ;
$locale_alert = CAppUI::conf("locale_alert");
CAppUI::$locale_mask = $locale_warn ? "$locale_alert%s$locale_alert" : "%s";

/*if ($locale_warn) {
  $s = '<form name="locale-edit-form" action="" onsubmit="onSubmitFormAjax(this)">
          <span ondblclick="$(this).hide();">%s</span>
          <input type="text" name="locale" value="" style="display: none;" />
        </form>';
  CAppUI::$locale_mask = $s;
} else {
  CAppUI::$locale_mask = "%s";
}*/
?>