<?php
/**
 * Error handlers and configuration
 * 
 * @package    Mediboard
 * @subpackage includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Id$
 */

global $dPconfig;

define("LOG_PATH", $dPconfig["root_dir"]."/tmp/mb-log.html");

require_once $dPconfig["root_dir"]."/classes/CError.class.php";

// Do not set to E_STRICT as it hides fatal errors to our error handler

// Developement
//error_reporting(E_ALL | E_STRICT | E_USER_DEPRECATED | E_DEPRECATED);

// Production 
error_reporting(E_ALL);

ini_set("error_log", LOG_PATH);
ini_set("log_errors_max_len", "4M");
ini_set("log_errors", true);
ini_set("display_errors", $dPconfig["debug"]);

/**
 * Get the path relative to Mediboard root
 * 
 * @param string $absPath Absolute path
 * 
 * @return string Relative path
 * @todo Move to CMbPath
 */ 
function mbRelativePath($absPath) {
  global $dPconfig;
  $mbPath = $dPconfig["root_dir"];
  
  $absPath = strtr($absPath, "\\", "/");
  $mbPath = strtr($mbPath, "\\", "/");
  
  // Hack for MS Windows server
  
  $relPath = strpos($absPath, $mbPath) === 0 ? 
    substr($absPath, strlen($mbPath) + 1) :
    $absPath;
    
  return $relPath;
}

/**
 * Traces variable using preformated text prefixed with a label
 * 
 * @param mixed  $var   Data to dump
 * @param string $label Add an optional label
 * 
 * @return void 
 **/
function mbDump($var, $label = null) {
  $errorTime = date("Y-m-d H:i:s");
  $msg = "<tt>[$errorTime] $label:</tt>";
  echo $msg;
  var_dump($var);
}

/**
 * Process the exported data
 * 
 * @param string $export Data
 * @param string $label  Add an optionnal label
 * @param bool   $log    Log to file or echo data
 * 
 * @return int The size of the data written in the log file
 **/
function processLog($export, $label = null, $log = false) {
  $export = CMbString::htmlSpecialChars($export);
  $time = date("Y-m-d H:i:s");
  $msg = "\n<pre>[$time] $label: $export</pre>";
  
  if ($log) {
    return file_put_contents(LOG_PATH, $msg, FILE_APPEND);
  }

  echo $msg;
  return null;
}

/**
 * Traces variable using preformated text prefixed with a label
 * 
 * @param mixed  $var   Data to dump
 * @param string $label Add an optional label
 * @param bool   $log   Log to file or echo data
 * 
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function mbTrace($var, $label = null, $log = false) {
  return processLog(print_r($var, true), $label, $log);
}

/**
 * Log shortcut to mbTrace
 * 
 * @param mixed  $var   Data to dump
 * @param string $label Add an optional label
 * 
 * @return int The size of the data written in the log file 
 **/
function mbLog($var, $label = null) {
  return mbTrace($var, $label, true);
}

/**
 * Traces variable using preformated text prefixed with a label
 * 
 * @param mixed  $var   Data to dump
 * @param string $label Add an optional label
 * @param bool   $log   Log to file or echo data
 * 
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function mbExport($var, $label = null, $log = false) {
  return processLog(var_export($var, true), $label, $log);
}

/**
 * Hide password param in HTTP param string
 * 
 * @param string $str HTTP params
 * 
 * @return string Sanitized HTTP
 **/
function hideUrlPassword($str) {
  return preg_replace("/(.*)password=([^&]+)(.*)/", '$1password=***$3', $str);
}

/**
 * Get HTML rendered information for serveur vars
 * 
 * @param array  $var  HTTP params
 * @param string $name Optional var name
 * 
 * @return string|null HTML
 **/
function print_infos($var, $name = '') {
  if (empty($var)) {
    return null;
  }
  
  $ret = "\n<pre>";
  $ret.= "<a href='#1' onclick='return toggle_info(this)'>$name</a>";
  
  if ($name == "GET") {
    $http_query = http_build_query($var, true, "&");
    $ret.= " - <a href='?$http_query' target='_blank'>Link</a>";
  }
  
  $info = substr(print_r(array_map_recursive(array("CMbString", "HTMLEntities"), $var), true), 6);
  $ret.= "<span style='display:none;'>$info</span></pre>";
  return $ret;
}

/**
 * Hide some params according to regexp
 *
 * @param array &$params Params to check
 *
 * @return void
 */
function filterInput(&$params) {
  $patterns = array(
    "/password|passphrase|pwd/i",
    "/login/i"
  );

  $replacements = array(
    array("/.*/", "***"),
    array("/([^:]*):(.*)/i", "$1:***")
  );

  // We replace passwords with a mask
  foreach ($params as $_type => $_params) {
    foreach ($_params as $_key => $_value) {
      foreach ($patterns as $_k => $_pattern) {
        if (!empty($_value) && preg_match($_pattern, $_key)) {
          $params[$_type][$_key] = preg_replace($replacements[$_k][0], $replacements[$_k][1], $_value);
        }
      }
    }
  }
}

/**
 * Custom error handler with backtrace
 * 
 * @param string $code      Error code
 * @param string $text      Error text
 * @param string $file      Error file path
 * @param string $line      Error line number
 * @param string $context   Error context
 * @param string $backtrace Error backtrace
 * 
 * @return void
 */
function errorHandler($code, $text, $file, $line, $context, $backtrace = null, $debug = false) {
  global $dPconfig;

  // Handles the @ case
  if (!error_reporting() || in_array($code, CError::$_excluded)) {
    return;
  }

  $old_error_reporting = error_reporting(0);

  $time = date("Y-m-d H:i:s");

  // User information
  $user_id = null;
  $user_view = "";
  if (class_exists("CAppUI", false) && CAppUI::$user) {
    $user = CAppUI::$user;
    if ($user->_id) {
      $user_id   = $user->_id;
      $user_view = $user->_view;
    }
  }

  // Server IP
  $server_ip = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : null;

  $file = mbRelativePath($file);
  $type = isset(CError::$_types[$code]) ? CError::$_types[$code] : null;

  // Stacktrace
  $contexts = $backtrace ? $backtrace : debug_backtrace();

  array_shift($contexts); // Remove current method from the stack

  foreach ($contexts as &$ctx) {
    if (isset($ctx["file"])) {
      $ctx["file"] = mbRelativePath($ctx["file"]);
    }
    unset($ctx['args']);
    unset($ctx['object']);
  }

  // Might noy be ready at the time error is thrown
  $session = isset($_SESSION) ? $_SESSION : array();
  unset($session['AppUI']);
  unset($session['dPcompteRendu']['templateManager']);

  $_all_params = array(
    "GET"     => $_GET,
    "POST"    => $_POST,
    "SESSION" => $session,
  );

  filterInput($_all_params);

  // CApp might not be ready yet as of early error handling
  $request_uid = null;
  if (class_exists("CApp", false)) {
    $request_uid = CApp::getRequestUID();
    CApp::$performance[CError::$_categories[$code]]++;
  }

  $build_output = ini_get("display_errors");
  $save_to_file = false;

  $data = array(
    "stacktrace"   => $contexts,
    "param_GET"    => $_all_params["GET"],
    "param_POST"   => $_all_params["POST"],
    "session_data" => $_all_params["SESSION"],
  );

  if (@$dPconfig["error_logs_in_db"] && class_exists("CErrorLog")) {
    try {
      CErrorLog::insert(
        $user_id, $server_ip,
        $time, $request_uid, $type, $text,
        $file, $line, $data, $debug
      );
    }
    catch (Exception $e) {
      $build_output = true;
      $save_to_file = true;
    }
  }
  else {
    $build_output = true;
    $save_to_file = true;
  }

  if ($build_output) {
    $hash = md5($code.$text.$file.$line.serialize($contexts));
    $html_class = isset(CError::$_classes[$code]) ? CError::$_classes[$code] : null;
    $log = "\n\n<div class='$html_class' title='$hash'>";

    if ($user_id) {
      $log .= "\n<strong>User: </strong>$user_view ($user_id)";
    }

    $file = CError::openInIDE($file, $line);

    $log .= <<<HTML
  <strong>Time: </strong>$time
  <strong>Type: </strong>$type
  <strong>Text: </strong>$text
  <strong>File: </strong>$file
  <strong>Line: </strong>$line
HTML;

    foreach ($_all_params as $_type => $_params) {
      $log .= print_infos($_all_params[$_type], $_type);
    }

    foreach ($contexts as $context) {
      $function = isset($context["class"]) ? $context["class"] . ":" : "";
      $function.= $context["function"] . "()";

      $log .= "\n<strong>Function: </strong> $function";

      if (isset($context["file"])) {
        $log .= "\n<strong>File: </strong>" . CError::openInIDE($context["file"], isset($context["line"]) ? $context["line"] : null);
      }

      if (isset($context["line"])) {
        $log .= "\n<strong>Line: </strong>" . $context["line"];
      }

      $log .= "<br />";
    }

    $log .= "</div>";

    if ($save_to_file) {
      file_put_contents(LOG_PATH, $log, FILE_APPEND);
    }

    if (ini_get("display_errors")) {
      echo $log;
    }
  }

  error_reporting($old_error_reporting);
}

set_error_handler("errorHandler");

/**
 * Custom exception handler with backtrace
 * 
 * @param exception $exception Thrown exception
 * 
 * @return void
 */
function exceptionHandler($exception) {
  global $dPconfig;

  $time = date("Y-m-d H:i:s");

  // User information
  $user_id = null;
  $user_view = "";
  if (class_exists("CAppUI", false) && CAppUI::$user) {
    $user = CAppUI::$user;
    if ($user->_id) {
      $user_id   = $user->_id;
      $user_view = $user->_view;
    }
  }

  // Server IP
  $server_ip = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : null;

  $file = mbRelativePath($exception->getFile());
  $line = $exception->getLine();
  $type = "exception";
  $text = htmlspecialchars($exception->getMessage());

  // Stacktrace
  $contexts = $exception->getTrace();
  foreach ($contexts as &$ctx) {
    unset($ctx['args']);
  }

  // Might noy be ready at the time error is thrown
  $session = isset($_SESSION) ? $_SESSION : array();
  unset($session['AppUI']);
  unset($session['dPcompteRendu']['templateManager']);

  $_all_params = array(
    "GET"     => $_GET,
    "POST"    => $_POST,
    "SESSION" => $session,
  );

  filterInput($_all_params);

  // CApp might not be ready yet as of early error handling
  $request_uid = null;
  if (class_exists("CApp", false)) {
    $request_uid = CApp::getRequestUID();
    CApp::$performance[CError::$_categories["exception"]]++;
  }

  $build_output = ini_get("display_errors");
  $save_to_file = false;

  $data = array(
    "stacktrace"   => $contexts,
    "param_GET"    => $_all_params["GET"],
    "param_POST"   => $_all_params["POST"],
    "session_data" => $_all_params["SESSION"],
  );

  if (@$dPconfig["error_logs_in_db"] && class_exists("CErrorLog")) {
    try {
      CErrorLog::insert(
        $user_id, $server_ip,
        $time, $request_uid, $type, $text,
        $file, $line, $data
      );
    }
    catch (Exception $e) {
      $build_output = true;
      $save_to_file = true;
    }
  }
  else {
    $build_output = true;
    $save_to_file = true;
  }

  if ($build_output) {
    $hash = md5(serialize($contexts));
    $html_class = "big-warning";
    $log = "\n\n<div class='$html_class' title='$hash'>";

    if ($user_id) {
      $log .= "\n<strong>User: </strong>$user_view ($user_id)";
    }

    $file = CError::openInIDE($file, $line);

    $log .= <<<HTML
  <strong>Time: </strong>$time
  <strong>Type: </strong>$type
  <strong>Text: </strong>$text
  <strong>File: </strong>$file
  <strong>Line: </strong>$line
HTML;

    foreach ($_all_params as $_type => $_params) {
      $log .= print_infos($_all_params[$_type], $_type);
    }

    foreach ($contexts as $context) {
      $function = isset($context["class"]) ? $context["class"] . ":" : "";
      $function.= $context["function"] . "()";

      $log .= "\n<strong>Function: </strong> $function";

      if (isset($context["file"])) {
        $log .= "\n<strong>File: </strong>" . CError::openInIDE($context["file"], isset($context["line"]) ? $context["line"] : null);
      }

      if (isset($context["line"])) {
        $log .= "\n<strong>Line: </strong>" . $context["line"];
      }

      $log .= "<br />";
    }

    $log .= "</div>";

    if ($save_to_file) {
      file_put_contents(LOG_PATH, $log, FILE_APPEND);
    }

    if (ini_get("display_errors")) {
      echo $log;
    }
  }
} 

set_exception_handler("exceptionHandler");

build_error_log();
