<?php /* CLASSES $Id$ */

/**
 * @package dotproject
 * @subpackage core
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @author Thomas Despoix (based on work of Andrew Eddie)
 * @version $Revision: 1765 $
 **/

// Message No Constants
define("UI_MSG_OK"     , 1);
define("UI_MSG_ALERT"  , 2);
define("UI_MSG_WARNING", 3);
define("UI_MSG_ERROR"  , 4);

// global variable holding the translation array
$GLOBALS["translate"] = array();

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
 * 
 * @TODO Should at least be static
 * @TODO Is being split into CApp et CUI classes
 */
class CAppUI {
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
  var $cfg = array();
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
      foreach (glob($dir) as $fileName) {
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
      return require_once("$root/lib/$name.php");
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
    global $AppUI;

    // define bad characters and their replacement
    $bad_chars = ";.\\";
    $bad_replace = "..."; // Needs the same number of chars as $bad_chars

    // check whether the filename contained bad characters
    if (strpos(strtr($file, $bad_chars, $bad_replace), ".") !== false) {
      $AppUI->redirect("m=system&a=access_denied");
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
  function savePlace($query="") {
    if (!$query) {
      $query = @$_SERVER["QUERY_STRING"];
    }
    if ($query != @$this->state["SAVEDPLACE"]) {
      $this->state["SAVEDPLACE-1"] = @$this->state["SAVEDPLACE"];
      $this->state["SAVEDPLACE"] = $query;
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
  function redirect($params="", $hist="") {
    $session_id = SID;

    session_write_close();
  
    // are the params empty
    if (!$params) {
    // has a place been saved
      $params = !empty($this->state["SAVEDPLACE$hist"]) ? $this->state["SAVEDPLACE$hist"] : $this->defaultRedirect;
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
  function setMsg($msg, $type = UI_MSG_OK) {
    // Formatage
    $args = func_get_args();
    $args[0] = CAppUI::tr($msg);
    unset($args[1]);
    $msg = call_user_func_array("sprintf", $args);

    // Ajout
    @$this->messages[$type][$msg]++;
  }
  
  function isMsgOK() {
    $errors = 0;
    $errors += count(@$this->messages[UI_MSG_ALERT]);
    $errors += count(@$this->messages[UI_MSG_WARNING]);
    $errors += count(@$this->messages[UI_MSG_ERROR]);
    return $errors == 0;
  }
  
  /*
   * Retourne le message résultant de la modification
   * d'un objet
   * @param string $msg résultat de la modification
   * @param string $action message à afficher
   */
  function displayMsg($msg, $action) {
    $action = $this->tr($action);
    if($msg){
      $this->setMsg("$action: $msg", UI_MSG_ERROR );
    }
    $this->setMsg($action, UI_MSG_OK );
  }

  /**
   * Display the formatted message and icon
   * @param boolean $reset If true the system UI is cleared
   */
  function getMsg($reset = true) {
    $return = "";
    
    ksort($this->messages);
    
    foreach ($this->messages as $type => $messages) {
      switch ($type) {
        case UI_MSG_OK      : $class = "message"; break;
        case UI_MSG_ALERT   : $class = "message"; break;
        case UI_MSG_WARNING : $class = "warning"; break;
        case UI_MSG_ERROR   : $class = "error" ; break;
        default: $class = "message"; break;
      }

      foreach ($messages as $message => $count) {
        $render = $count > 1 ? "$message x $count" : $message;
        $return .= "<div class='$class'>$render</div>";
      }
      
    }
    
    if ($reset) {
      $this->messages = array();
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
      case UI_MSG_OK      : $class = "big-message"; break;
      case UI_MSG_WARNING : $class = "big-warning"; break;
      case UI_MSG_ERROR   : $class = "big-error" ; break;
      default: $class = "big-message"; break;
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
      case UI_MSG_OK      : $class = "message"; break;
      case UI_MSG_WARNING : $class = "warning"; break;
      case UI_MSG_ERROR   : $class = "error" ; break;
      default: $class = "message"; break;
    }
    
    // Formatage
    $args = func_get_args();
    $args[0] = CAppUI::tr($msg);
    unset($args[1]);
    $msg = call_user_func_array("sprintf", $args);
    
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
  static function callbackAjax($callback, $value) {
    $value = smarty_modifier_json($value);
    echo "\n<script type='text/javascript'>$callback($value);</script>";
  }
  
  
/**
 * Login function
 *
 * Upon a successful username and password match, several fields from the user
 * table are loaded in this object for convenient reference.  The style, localces
 * and preferences are also loaded at this time.
 *
 * @param string The user login name
 * @param string The user password
 * @return boolean True if successful, false if not
 */
  function login() {
  	$ds = CSQLDataSource::get("std");
    // Test login and password validity
    $user = new CUser;
    $user->user_username  = trim(mbGetValueFromPost("username"));
    $user->_user_password = trim(mbGetValueFromPost("password"));
    
    $specsObj = $user->getSpecsObj();
    $pwdSpecs = $specsObj['_user_password']; // Spec du mot de passe sans _
    
    // Login as, for administators
    if ($loginas = mbGetValueFromPost("loginas")) {
      if ($this->user_type != 1) {
        $this->setMsg("Auth-failed-loginas-admin", UI_MSG_ERROR);
        return false;
      }
      
      $user->user_username = trim($loginas);
      $user->_user_password = null;
    } 
    // No password given
    elseif (!$user->_user_password) {
      $this->setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
      return false;
    }
    
    $pwd = $user->_user_password; // Le mot de passe récupéré est avec un _
    
    if ($pwd) {
	    $this->weak_password = false;
	    // minLength
	    if ($pwdSpecs->minLength > strlen($pwd)) {
	      $this->weak_password = true;
	    }
	
	    // notContaining
	    if($pwdSpecs->notContaining) {
	      $target = $pwdSpecs->notContaining;
	        if ($field = $user->$target)
	          if (stristr($pwd, $field))
	            $this->weak_password = true;
	    }
	    
	    // notNear
	    if($pwdSpecs->notNear) {
	      $target = $pwdSpecs->notNear;
	        if ($field = $user->$target)
	          if (levenshtein($pwd, $field) < 3)
	            $this->weak_password = true;
	    }
	
	    // alphaAndNum
	    if($pwdSpecs->alphaAndNum) {
	      if (!preg_match("/[A-z]/", strtolower($pwd)) || !preg_match("/\d+/", $pwd)) {
	        $this->weak_password = true;
	      }
	    }
    }
    
    // See CUser::updateDBFields
    $user->loadMatchingObject();
    
    $userByName = new CUser;
    $userByName->user_username = trim(mbGetValueFromPost("username"));
    $userByName->loadMatchingObject();
    
    if ($userByName->_login_locked) {
      $this->setMsg("Auth-failed-user-locked", UI_MSG_ERROR);
      return false;
    }
    
    // Wrong login and/or password
    $loginErrorsReady = $user->loginErrorsReady();
    if (!$user->_id) {
      $this->setMsg("Auth-failed-combination", UI_MSG_ERROR);

      // If the user exists, but has given a wrong password let's increment his error count
	    if ($loginErrorsReady && $userByName->_id && !$userByName->_login_locked) {
	      $userByName->user_login_errors++;
	      $userByName->store();
	      $remainingAttempts = max(0, CAppUI::conf("admin CUser max_login_attempts")-$userByName->user_login_errors);
	      $this->setMsg("Auth-failed-tried", UI_MSG_ERROR, $userByName->user_login_errors, $remainingAttempts);
	    }
      return false;
      
    } 
    // User not locked and has given a good password
    else {
      if ($loginErrorsReady) {
	      $user->user_login_errors = 0;
	      $user->store();
      }
    }
    
    // Put user_group in AppUI
    $this->user_remote = 1;
    if ($ds->loadTable("users_mediboard") && $ds->loadTable("groups_mediboard")) {
      $sql = "SELECT `remote` FROM `users_mediboard` WHERE `user_id` = '$user->user_id'";
      if ($cur = $ds->exec($sql)) {
        if ($row = $ds->fetchRow($cur)) {
          $this->user_remote = intval($row[0]);
        }
      }
      $sql = "SELECT `groups_mediboard`.`group_id`" .
          "\nFROM `groups_mediboard`, `functions_mediboard`, `users_mediboard`" .
          "\nWHERE `groups_mediboard`.`group_id` = `functions_mediboard`.`group_id`" .
          "\nAND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`" .
          "\nAND `users_mediboard`.`user_id` = '$user->user_id'";
      $this->user_group = $ds->loadResult($sql);
    }
    
    // Test if remote connection is allowed
    $this->_is_intranet = is_intranet_ip($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] != CAppUI::conf("system reverse_proxy")); 
    if (!$this->_is_intranet && $this->user_remote == 1 && $user->user_type != 1) {
      $this->setMsg("User has no remote access", UI_MSG_ERROR);
      return false;
    }

    $this->user_id   = $user->_id;
    
    
    // DEPRECATED
    $this->user_first_name = $user->user_first_name;
    $this->user_last_name  = $user->user_last_name;
    $this->user_email      = $user->user_email;
    $this->user_type       = $user->user_type;
    $this->user_last_login = $user->user_last_login;
    // END DEPRECATED
    
    // save the last_login dateTime
    if($ds->loadField("users", "user_last_login")) {
      // Nullify password or you md5 it once more
      $user->user_last_name = null;
      $user->user_last_login = mbDateTime();
      $user->store();
    }

    // load the user preferences
    $this->loadPrefs($this->user_id);
    return true;
  }

/**
 * Load the stored user preferences from the database into the internal
 * preferences variable.
 * @param int $uid User id number, 0 for default preferences
 */
  function loadPrefs($uid = 0) {
  	$ds = CSQLDataSource::get("std");
    $sql = "SELECT pref_name, pref_value FROM user_preferences WHERE pref_user = '$uid'";
    $user_prefs = $ds->loadHashList($sql);
    $this->user_prefs = array_merge($this->user_prefs, $user_prefs);
  }
  
  /**
   * Attempt to make AppUI functions static for better use
   */

  /**
   * Translate given statement
   * @param string $str statement to translate
   * @return string translated statement
   */
  static function tr($str) {
    $str = trim($str);
    if (empty($str)) {
      return "";
    }
    
    // Defined and not empty
    if (isset($GLOBALS["translate"][$str]) && $GLOBALS["translate"][$str] != "") {
      return $GLOBALS["translate"][$str];
    }

    return nl2br(sprintf(self::$locale_mask, $str));
  }

  /**
   * Return the configuration setting for a given path
   * @param $path string Tokenized path, eg "module class var";
   * @return mixed scalar or array of values depending on the path
   */
  static function conf($path = '') {
    global $dPconfig;
    $conf = $dPconfig;
    $items = explode(' ', $path);
    foreach ($items as $part) {
    	$conf = $conf[$part];
    }
    return $conf;
  }
  
  static function unique_id() {
    return self::$unique_id++;
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