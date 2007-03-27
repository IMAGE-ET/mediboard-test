<?php /* CLASSES $Id$ */

/**
* @package dotproject
* @subpackage core
* @license http://opensource.org/licenses/bsd-license.php BSD License
*/

// Message No Constants
define("UI_MSG_OK"     , 1);
define("UI_MSG_ALERT"  , 2);
define("UI_MSG_WARNING", 3);
define("UI_MSG_ERROR"  , 4);

// global variable holding the translation array
$GLOBALS["translate"] = array();

define("UI_CASE_UPPER"     , 1);
define("UI_CASE_LOWER"     , 2);
define("UI_CASE_UPPERFIRST", 3);

/**
* The Application User Interface Class.
*
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
* @version $Revision$
*/
class CAppUI {
/** @var array generic array for holding the state of anything */
  var $state = null;
/** @var int */
  var $user_id = 0;
/** @var string */
  var $user_first_name = null;
/** @var string */
  var $user_last_name = null;
/** @var string */
  var $user_email = null;
/** @var int */
  var $user_type = null;
/** @var int */
  var $user_group = null;
/** @var dateTime */
  var $user_last_login = null;
/** @var array */
  var $user_prefs=null;
/** @var int Unix time stamp */
  var $day_selected=null;

// localisation
/** @var string */
  var $user_locale=null;
/** @var string */
  var $base_locale = "en"; // do not change - the base "keys" will always be in english

/** @var string Message string*/
  var $msg = "";
/** @var string */
  var $msgNo = "";
/** @var string Default page for a redirect call*/
  var $defaultRedirect = "";

/** @var array Configuration variable array*/
  var $cfg=null;

/** @var integer Version major */
  var $version_major = null;

/** @var integer Version minor */
  var $version_minor = null;

/** @var integer Version patch level */
  var $version_patch = null;

/** @var string Version string */
  var $version_string = null;

/**
* CAppUI Constructor
*/
  function CAppUI() {
    $this->state = array();

    $this->user_first_name = "";
    $this->user_last_name = "";
    $this->user_type = 0;

    $this->project_id = 0;

    $this->defaultRedirect = "";
// set up the default preferences
    $this->user_locale = $this->base_locale;
    $this->user_prefs = array();
  }
  
  function getAllClasses() {
    $rootDir = $this->getConfig("root_dir");
    foreach(glob("classes/*/*.class.php") as $fileName) {
      require_once("$rootDir/$fileName");
    }
    // Require all global classes
    foreach(glob("classes/*.class.php") as $fileName) {
      require_once("$rootDir/$fileName");
    }
    // Require all modules classes
    foreach(glob("modules/*/*.class.php") as $fileName) {
      require_once("$rootDir/$fileName");
    }
  }

/**
* Used to load a php class file from the system classes directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
  function getSystemClass($name=null) {
    if ($name) {
      if ($root = $this->getConfig("root_dir")) {
        return "$root/classes/$name.class.php";
      }
    }
  }

/**
 * Used to load a php class file from the legacy classes directory
 * @param string <b>$name</b> The class file name (excluding .class.php)
 * @return string The path to the include file
 */
  function getLegacyClass($name=null) {
    if ($name) {
      if ($root = $this->getConfig("root_dir")) {
        return "$root/legacy/$name.class.php";
      }
    }
  }

/**
 * Used to load a php class file from the lib directory
 *
 * @param string <b>$name</b> The class root file name (excluding .php)
 * @return string The path to the include file
 */
  function getLibraryFile($name=null) {
    if ($name) {
      if ($root = $this->getConfig("root_dir")) {
        return "$root/lib/$name.php";
      }
    }
  }

/**
* Used to load a php class file from the module directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
  function getModuleClass($name = null, $file = null) {
    if ($name) {
      if ($root = $this->getConfig("root_dir")) {
        $filename = $file ? $file : $name;
        return "$root/modules/$name/$filename.class.php";
      }
    }
  }

/**
* Used to load a php file from the module directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
  function getModuleFile($name = null, $file = null) {
    if ($name) {
      if ($root = $this->getConfig("root_dir")) {
        $filename = $file ? $file : $name;
        return "$root/modules/$name/$filename.php";
      }
    }
  }
  
/**
* Used to store information in tmp directory
* @param string $subpath in tmp directory
* @return string The path to the include file
 */
  function getTmpPath($subpath) {
    if ($subpath) {
      if ($root = $this->getConfig("root_dir")) {
        return "$root/tmp/$subpath";
      }
    }
  }
  

/**
* Sets the internal confuration settings array.
* @param array A named array of configuration variables (usually from config.php)
*/
  function setConfig(&$cfg) {
    $this->cfg = $cfg;
  }

/**
* Retrieves a configuration setting.
* @param string The name of a configuration setting
* @return The value of the setting, otherwise null if the key is not found in the configuration array
*/
  function getConfig($key) {
    if (array_key_exists($key, $this->cfg)) {
      return $this->cfg[$key];
    } else {
      return null;
    }
  }

/**
* Determines the version.
* @return String value indicating the current dotproject version
*/
  function getVersion() {
    if (! isset($this->version_major)) {
      include_once $this->cfg["root_dir"] . "/includes/version.php";
      $this->version_major = $dp_version_major;
      $this->version_minor = $dp_version_minor;
      $this->version_patch = $dp_version_patch;
      $this->version_string = $this->version_major . "." . $this->version_minor;
      if (isset($this->version_patch))
        $this->version_string .= "." . $this->version_patch;
      if (isset($dp_version_prepatch))
        $this->version_string .= "-" . $dp_version_prepatch;
    }
    return $this->version_string;
  }

/**
* Checks that the current user preferred style is valid/exists.
*/
  function checkStyle() {
    // check if default user's uistyle is installed
    $uistyle = $this->getPref("UISTYLE");

    if ($uistyle && !is_dir($this->cfg["root_dir"]."/style/$uistyle")) {
      // fall back to host_style if user style is not installed
      $this->setPref("UISTYLE", $this->cfg["host_style"]);
    }
  }

/**
* Utility function to read the "directories" under "path"
*
* This function is used to read the modules or locales installed on the file system.
* @param string The path to read.
* @return array A named array of the directories (the key and value are identical).
*/
  function readDirs($path) {
    $dirs = array();
    $d = dir($this->cfg["root_dir"]."/$path");
    while (false !== ($name = $d->read())) {
      if(is_dir($this->cfg["root_dir"]."/$path/$name") && $name != "." && $name != ".." && $name != "CVS") {
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
  function readFiles($path, $filter=".") {
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
  function checkFileName($file) {
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
* Sets the user locale.
*
* Looks in the user preferences first.  If this value has not been set by the user it uses the system default set in config.php.
* @param string Locale abbreviation corresponding to the sub-directory name in the locales directory (usually the abbreviated language code).
*/
  function setUserLocale() {
    $this->user_locale = $this->user_prefs["LOCALE"];
  }

/**
* Translate string to the local language [same form as the gettext abbreviation]
*
* This is the order of precedence:
* <ul>
* <li>If the key exists in the lang array, return the value of the key
* <li>If no key exists and the base lang is the same as the local lang, just return the string
* <li>If this is not the base lang, then return string with a red star appended to show
* that a translation is required.
* </ul>
* @param string The string to translate
* @param int Option to change the case of the string
* @return string
*/
  function _($str, $case=0) {
    $str = trim($str);
    if (empty($str)) {
      return "";
    }
    $x = @$GLOBALS["translate"][$str];
    if ($x) {
      $str = $x;
    } else if (@$this->cfg["locale_warn"]) {
      if ($this->base_locale != $this->user_locale ||
        ($this->base_locale == $this->user_locale && !in_array($str, @$GLOBALS["translate"]))) {
        $str .= @$this->cfg["locale_alert"];
      }
    }
    switch ($case) {
      case UI_CASE_UPPER:
        $str = strtoupper($str);
        break;
      case UI_CASE_LOWER:
        $str = strtolower($str);
        break;
      case UI_CASE_UPPERFIRST:
        break;
    }
    /* stripslashes added to fix #811242 on 2004 Jan 10
     * if no problems occur, delete this comment. (gregor) */
    return $str;
  }
/**
* Set the display of warning for untranslated strings
* @param string
*/
  function setWarning($state=true) {
    $temp = @$this->cfg["locale_warn"];
    $this->cfg["locale_warn"] = $state;
    return $temp;
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
    exit(); // stop the PHP execution
  }
/**
* Set the page message.
*
* The page message is displayed above the title block and then again
* at the end of the page.
*
* IMPORTANT: Please note that append should not be used, since for some
* languagues atomic-wise translation doesn't work. Append should be
* deprecated.
*
* @param string The (translated) message
* @param int The type of message
* @param boolean If true, $msg is appended to the current string otherwise
* the existing message is overwritten with $msg.
*/
  function setMsg($msg, $msgNo = null, $append = false) {
    $msg = $this->_($msg);
    $this->msg = ($append and $this->msg) ? join(array($this->msg, $msg), "\n") : $msg;

    if ($msgNo) {
      $this->msgNo = $msgNo;
    }
  }
  
 /**
  * Display the formatted message and icon
  * @param boolean If true the current message state is cleared.
  */
  function getMsg($reset=true) {
    $msg = $this->msg;

    switch($this->msgNo) {
      case UI_MSG_OK      : $class = "message"; break;
      case UI_MSG_ALERT   : $class = "message"; break;
      case UI_MSG_WARNING : $class = "warning"; break;
      case UI_MSG_ERROR   : $class = "error" ; break;
      default: $class = "message"; break;
    }

    if ($reset) {
      $this->msg = "";
      $this->msgNo = 0;
    }
    

    return $msg ? "<div class='$class'>$msg</div>" : "";
  }

 /**
  * Display an ajax step, and exit on error messages
  * @param string $msg : the message
  * @param enum $msgType : type of message [UI_MSG_OK|UI_MSG_WARNING|UI_MSG_ERROR]
  */
  function stepAjax($msg, $msgType = UI_MSG_OK) {
    switch($msgType) {
      case UI_MSG_OK      : $class = "message"; break;
      case UI_MSG_WARNING : $class = "warning"; break;
      case UI_MSG_ERROR   : $class = "error" ; break;
      default: $class = "message"; break;
    }
    
    $msg = nl2br($msg);

    echo "<div class='$class'>$msg</div>";
    
    if ($msgType == UI_MSG_ERROR) {
      die;
    }
  }

/**
* Set the value of a temporary state variable.
*
* The state is only held for the duration of a session.  It is not stored in the database.
* @param string The label or key of the state variable
* @param mixed Value to assign to the label/key
*/
  function setState($label, $value) {
    $this->state[$label] = $value;
  }
/**
* Get the value of a temporary state variable.
* @return mixed
*/
  function getState($label) {
    return array_key_exists($label, $this->state) ? $this->state[$label] : null;
  }
/**
* Login function
*
* A number of things are done in this method to prevent illegal entry:
* <ul>
* <li>The username and password are trimmed and escaped to prevent malicious
*     SQL being executed
* <li>The username and encrypted password are selected from the database but
*     the comparision is not made by the database, for example
*     <code>...WHERE user_username = '$username' AND password=MD5('$password')...</code>
*     to further prevent the injection of malicious SQL
* </ul>
* The schema previously used the MySQL PASSWORD function for encryption.  This
* is not the recommended technique so a procedure was introduced to first check
* for a match using the PASSWORD function.  If this is successful, then the
* is upgraded to the MD5 encyption format.  This check can be controlled by the
* <code>check_legacy_password</code> configuration variable in </code>config.php</code>
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
    // Test login and password validity
    $user = new CUser;
    $user->user_username = trim(mbGetValueFromPost("username"));
    $user->_user_password = trim(mbGetValueFromPost("password"));
    
    // Login as, for administators
    if ($loginas = mbGetValueFromPost("loginas")) {
      if ($this->user_type != 1) {
        $this->setMsg("Only administrator can login as another user", UI_MSG_ERROR);
        return false;
      }
      
      $user->user_username = trim($loginas);
      $user->_user_password = null;
    } 
    // No password given
    elseif (!$user->_user_password) {
      $this->setMsg("You should enter your password", UI_MSG_ERROR);
      return false;
    }
        
    
    
    // See CUser::updateDBFields
    $user->loadMatchingObject();
    
    if (!$user->_id) {
      $this->setMsg("Wrong login/password combination", UI_MSG_ERROR);
      return false;
    }
    
    // Put user_group in AppUI
    $remote = 1;
    if (db_loadTable("users_mediboard") && db_loadTable("groups_mediboard")) {
      $sql = "SELECT `remote` FROM `users_mediboard` WHERE `user_id` = '$user->user_id'";
      if ($cur = db_exec($sql)) {
        if ($row = db_fetch_row($cur)) {
          $remote = intval($row[0]);
        }
      }
      $sql = "SELECT `groups_mediboard`.`group_id`" .
          "\nFROM `groups_mediboard`, `functions_mediboard`, `users_mediboard`" .
          "\nWHERE `groups_mediboard`.`group_id` = `functions_mediboard`.`group_id`" .
          "\nAND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`" .
          "\nAND `users_mediboard`.`user_id` = '$user->user_id'";
      $this->user_group = db_loadResult($sql);
    }
    
    // Test if remote connection is allowed
    $browserIP = explode(".", $_SERVER["REMOTE_ADDR"]);
    $ip0 = intval($browserIP[0]);
    $ip1 = intval($browserIP[1]);
    $ip2 = intval($browserIP[2]);
    $ip3 = intval($browserIP[3]);
    $is_local[1] = ($ip0 == 127 && $ip1 == 0 && $ip2 == 0 && $ip3 == 1); 
    $is_local[2] = ($ip0 == 10);
    $is_local[3] = ($ip0 == 172 && $ip1 >= 16 && $ip1 < 32);
    $is_local[4] = ($ip0 == 192 && $ip1 == 168);
    $is_local[0] = $is_local[1] || $is_local[2] || $is_local[3] || $is_local[4];
    if (!$is_local[0] && $remote == 1 && $user->user_type != 1) {
      $this->setMsg("User has no remote access", UI_MSG_ERROR);
      return false;
    }

    // Load the user in AppUI
    $this->user_id         = $user->user_id;
    $this->user_first_name = $user->user_first_name;
    $this->user_last_name  = $user->user_last_name;
    $this->user_email      = $user->user_email;
    $this->user_type       = $user->user_type;
    $this->user_last_login = $user->user_last_login;
    
    // save the last_login dateTime
    if(db_loadField("users", "user_last_login")) {
      // Nullify password or you md5 it once more
      $user->user_last_name = null;
      $user->user_last_login = mbDateTime();
      $user->store();
    }

    // load the user preferences
    $this->loadPrefs($this->user_id);
    $this->setUserLocale();
    $this->checkStyle();
    return true;
  }

/**
 * Gets the value of the specified user preference
 * @param string Name of the preference
 */
  function getPref($name) {
    return @$this->user_prefs[$name];
  }
/**
* Sets the value of a user preference specified by name
* @param string Name of the preference
* @param mixed The value of the preference
*/
  function setPref($name, $val) {
    $this->user_prefs[$name] = $val;
  }
/**
* Loads the stored user preferences from the database into the internal
* preferences variable.
* @param int User id number
*/
  function loadPrefs($uid=0) {
    $sql = "SELECT pref_name, pref_value FROM user_preferences WHERE pref_user = '$uid'";
    //writeDebug($sql, "Preferences for user $uid, SQL", __FILE__, __LINE__);
    $prefs = db_loadHashList($sql);
    $this->user_prefs = array_merge($this->user_prefs, db_loadHashList($sql));
  }
}

?>