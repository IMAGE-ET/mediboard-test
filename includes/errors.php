<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $dPconfig;

define("LOG_PATH", $dPconfig["root_dir"]."/tmp/mb-log.html");
define("E_JS_ERROR", 0);

// Since PHP 5.2.0
if (!defined("E_RECOVERABLE_ERROR")) {
  define("E_RECOVERABLE_ERROR", 4096);
}

// Since PHP 5.3.0
if (!defined("E_DEPRECATED")) {
  define("E_DEPRECATED", 8192);
  define("E_USER_DEPRECATED", 16384);
}

// Do not set to E_STRICT as it hides fatal errors to our error handler

// developement
//error_reporting(E_ALL | E_STRICT | E_USER_DEPRECATED | E_DEPRECATED);

// production 
error_reporting(E_ALL);

ini_set("error_log", LOG_PATH);
ini_set("log_errors_max_len", "4M");
ini_set("log_errors", true);
ini_set("display_errors", $dPconfig["debug"]);

$divClasses = array (
  E_ERROR => "big-error",                // 1
  E_WARNING => "big-warning",            // 2
  E_PARSE => "big-info",                 // 4
  E_NOTICE => "big-info",                // 8
  E_CORE_ERROR => "big-error",           // 16
  E_CORE_WARNING => "big-warning",       // 32
  E_COMPILE_ERROR => "big-error",        // 64
  E_COMPILE_WARNING => "big-warning",    // 128
  E_USER_ERROR => "big-error",           // 256
  E_USER_WARNING => "big-warning",       // 512
  E_USER_NOTICE => "big-info",           // 1024
  E_STRICT => "big-info",                // 2048
  E_RECOVERABLE_ERROR => "big-error",    // 4096
  E_DEPRECATED => "big-info",            // 8192
  E_USER_DEPRECATED => "big-info",       // 16384
                                 // E_ALL = 32767 (PHP 5.4)
  E_JS_ERROR => "big-warning javascript",// 0
);

// Pour BCB 
unset($divClasses[E_STRICT]);
unset($divClasses[E_DEPRECATED]);

if (!$dPconfig["debug"]) {
  unset($divClasses[E_STRICT]);
  unset($divClasses[E_RECOVERABLE_ERROR]); // Thrown by bad type hinting
}

$errorTypes = array (
  E_ERROR => "Error",
  E_WARNING => "Warning",
  E_PARSE => "Parse",
  E_NOTICE => "Notice",
  E_CORE_ERROR => "Core error",
  E_CORE_WARNING => "Core warning",
  E_COMPILE_ERROR => "Compile error",
  E_COMPILE_WARNING => "Compile warning",
  E_USER_ERROR => "User error",
  E_USER_WARNING => "User warning",
  E_USER_NOTICE => "User notice",
  E_STRICT => "Strict",
  E_RECOVERABLE_ERROR => "Recoverable error",
  E_DEPRECATED => "Deprecated",
  E_USER_DEPRECATED => "User deprecated",
  E_JS_ERROR => "Javascript error",
);

$errorCategories = array (
  E_ERROR => "error",
  E_WARNING => "warning",
  E_PARSE => "error",
  E_NOTICE => "notice",
  E_CORE_ERROR => "error",
  E_CORE_WARNING => "warning",
  E_COMPILE_ERROR => "error",
  E_COMPILE_WARNING => "warning",
  E_USER_ERROR => "error",
  E_USER_WARNING => "warning",
  E_USER_NOTICE => "notice",
  E_STRICT => "notice",
  E_RECOVERABLE_ERROR => "error",
  E_DEPRECATED => "notice",
  E_USER_DEPRECATED => "notice",
  E_JS_ERROR => "warning",
);

// To be put in mbFonctions
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
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function processLog($export, $label = null, $log = false) {
  $export = htmlspecialchars($export);
  $time = date("Y-m-d H:i:s");
  $msg = "\n<pre>[$time] $label: $export</pre>";
  
  if ($log) {
    return file_put_contents(LOG_PATH, $msg, FILE_APPEND);
  }
  echo $msg;
}

/**
 * Traces variable using preformated text prefixed with a label
 * @param any $var The variable you want to trace the value of
 * @param string $label Prefix with a label
 * @param bool $log Log the trace
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function mbTrace($var, $label = null, $log = false) {
  return processLog(print_r($var, true), $label, $log);
}

function mbLog($var, $label = null) {
  return mbTrace($var, $label, true);
}

/**
 * Traces variable using preformated text prefixed with a label
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function mbExport($var, $label = null, $log = false) {
  return processLog(var_export($var, true), $label, $log);
}

function print_infos($var, $name = '') {
  if (empty($var)) return;
  
  $ret = "\n<pre><a href='#1' onclick='var s=this.parentNode.getElementsByTagName(\"span\")[0].style;s.display=s.display==\"none\"?\"\":\"none\";return false;'>$name</a>";
  
  if ($name == "GET") {
    $ret .= " - <a href='?".http_build_query($var, true, "&")."' target='_blank'>Link</a>";
  }
  
  $ret .= "<span style='display:none;'> ".substr(print_r($var, true), 6).'</span></pre>';
  return $ret;
}

/**
 * Custom herror handler with backtrace
 * @return null
 */
function errorHandler($errorCode, $errorText, $errorFile, $errorLine, $errContext, $backTrace = null) {
  global $divClasses, $errorTypes, $errorCategories, $performance;
  
// See ALL errors
//  echo "<br />[$errno] : $errorText, $errorFile : $errorLine";
  
  // Handles the @ case
  if (!error_reporting() || !array_key_exists($errorCode, $divClasses)) {
    return;
  }
  
  $errorTime = date("Y-m-d H:i:s");
  
  // CMbArray non chargé
  $divClass = isset($divClasses[$errorCode]) ? $divClasses[$errorCode] : null;
  $errorType = isset($errorTypes[$errorCode]) ? $errorTypes[$errorCode] : null;
  
  // Contextes 
  $contexts = $backTrace ? $backTrace : debug_backtrace();
  foreach ($contexts as &$ctx) {
    unset($ctx['args']);
    unset($ctx['object']);
  }
  $hash = md5($errorCode.$errorText.$errorFile.$errorLine.serialize($contexts));
  
  array_shift($contexts);
  $log = "\n\n<div class='$divClass' title='$hash'>";
  
  if (class_exists("CUser")) {
    $user = CUser::get();
    if ($user->_id){
      $log .= "\n<strong>User: </strong>$user->_view ($user->_id)";
    }
  }

  // Erreur générale
  $errorFile = mbRelativePath($errorFile);
  $log .= "\n<strong>Time: </strong>$errorTime
             <strong>Type: </strong>$errorType
             <strong>Text: </strong>$errorText
             <strong>File: </strong>$errorFile
             <strong>Line: </strong>$errorLine";
  
  $log .= print_infos($_GET, 'GET');
  $log .= print_infos($_POST, 'POST');
  
  // Might noy be ready at the time error is thrown
  $session = isset($_SESSION) ? $_SESSION : array();
  unset($session['AppUI']);
  unset($session['dPcompteRendu']['templateManager']);
  $log .= print_infos($session, 'SESSION');
  
  foreach($contexts as $context) {
    $function = isset($context["class"]) ? $context["class"] . ":" : "";
    $function.= $context["function"] . "()";
    
    $log .= "\n<strong>Function: </strong> $function";
    
    if (isset($context["file"])) {
      $context["file"] = mbRelativePath($context["file"]);
      $log .= "\n<strong>File: </strong>" . $context["file"];      
    }
    
    if (isset($context["line"])) {
      $log .= "\n<strong>Line: </strong>" . $context["line"];
    }
    
    $log .= "<br />";
  }
  
  $log .= "</div>";
  
  $performance[$errorCategories[$errorCode]]++;
  
  if (ini_get("log_errors")) {
    file_put_contents(LOG_PATH, $log, FILE_APPEND);
  }
  
  if (ini_get("display_errors")) {
    echo $log;
  }
} 

set_error_handler("errorHandler");

/**
 * Custom exception handler with backtrace
 * @return null
 */
function exceptionHandler($exception) {
  global $divClasses, $errorTypes, $errorCategories;
  
  $divClass = "big-warning";
  
  // Contextes 
  $contexts = $exception->getTrace();
  foreach($contexts as &$ctx) {
    unset($ctx['args']);
  }
  $hash = md5(serialize($contexts));
  
  $log = "\n\n<div class='$divClass' title='$hash'>";
  
  $user = CUser::get();
  if ($user->_id){
    $log .= "\n<strong>User: </strong>$user->_view ($user->_id)";
  }
  
  // Erreur générale
  $errorTime = date("Y-m-d H:i:s");
  $errorType = "Exception";
  $errorFile = mbRelativePath($exception->getFile());
  $errorLine = $exception->getLine();
  $errorText = $exception->getMessage();
  $log .= "\n<strong>Time: </strong>$errorTime
             <strong>Type: </strong>$errorType
             <strong>Text: </strong>$errorText
             <strong>File: </strong>$errorFile
             <strong>Line: </strong>$errorLine";
             
  $log .= print_infos($_GET, 'GET');
  $log .= print_infos($_POST, 'POST');
  
  $session = $_SESSION;
  unset($session['AppUI']);
  unset($session['dPcompteRendu']['templateManager']);
  $log .= print_infos($session, 'SESSION');
  
  foreach($contexts as $context) {
    $function = isset($context["class"]) ? $context["class"] . ":" : "";
    $function.= $context["function"] . "()";
    
    $log .= "\n<strong>Function: </strong> $function";
    
    if (isset($context["file"])) {
      $context["file"] = mbRelativePath($context["file"]);
      $log .= "\n<strong>File: </strong>" . $context["file"];      
    }
    
    if (isset($context["line"])) {
      $log .= "\n<strong>Line: </strong>" . $context["line"];
    }
    
    $log .= "<br />";
  }
  
  $log .= "</div>";
  
  if (ini_get("log_errors")) {
    file_put_contents(LOG_PATH, $log, FILE_APPEND);
  }
  
  if (ini_get("display_errors")) {
    echo $log;
  }
} 

set_exception_handler("exceptionHandler");

// Initialize custom error handler
if (!is_file(LOG_PATH)) {
  $initTime = date("Y-m-d H:i:s");
  $logInit = "<h2>Log de Mediboard ré-initialisé depuis $initTime</h2>";
  file_put_contents(LOG_PATH, $logInit);
}
