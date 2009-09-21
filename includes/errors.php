<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $performance, $dPconfig;
$performance["error"] = 0;
$performance["warning"] = 0;
$performance["notice"] = 0;
define('LOG_PATH', $dPconfig["root_dir"]."/tmp/mb-log.html");
define('E_JS_ERROR', 0);

// Do not set to E_STRICT as it hides fatal errors to our error handler
// Strict warning will still be handle by our handler anyway

error_reporting(E_ALL);
ini_set("error_log", LOG_PATH);
ini_set("log_errors_max_len", "4M");
ini_set("log_errors", true);
ini_set("display_errors", $dPconfig["debug"]);

$divClasses = array (
  E_ERROR => "big-error",
  E_WARNING => "big-warning",
  E_NOTICE => "big-info",
  E_STRICT => "big-info",
  E_PARSE => "big-info",
  E_CORE_ERROR => "big-error",
  E_CORE_WARNING => "big-warning",
  E_COMPILE_ERROR => "big-error",
  E_COMPILE_WARNING => "big-warning",
  E_USER_ERROR => "big-error",
  E_USER_WARNING => "big-warning",
  E_USER_NOTICE => "big-info",
  E_JS_ERROR => "big-warning",
);

// Pour BCB 
unset($divClasses[E_STRICT]);

if (!$dPconfig["debug"]) {
  unset($divClasses[E_STRICT]);
}

$errorTypes = array (
  E_ERROR => "Error",
  E_WARNING => "Warning",
  E_NOTICE => "Notice",
  E_STRICT => "Strict",
  E_PARSE => "Parse",
  E_CORE_ERROR => "Core error",
  E_CORE_WARNING => "Core warning",
  E_COMPILE_ERROR => "Compile error",
  E_COMPILE_WARNING => "Compile warning",
  E_USER_ERROR => "User error",
  E_USER_WARNING => "User warning",
  E_USER_NOTICE => "User notice",
  E_JS_ERROR => "Javascript error",
);

$errorCategories = array (
  E_ERROR => "error",
  E_WARNING => "warning",
  E_NOTICE => "notice",
  E_STRICT => "notice",
  E_PARSE => "error",
  E_CORE_ERROR => "error",
  E_CORE_WARNING => "warning",
  E_COMPILE_ERROR => "error",
  E_COMPILE_WARNING => "warning",
  E_USER_ERROR => "error",
  E_USER_WARNING => "warning",
  E_USER_NOTICE => "notice",
  E_JS_ERROR => "warning",
);

// To be put in mbFonctions
function mbRelativePath($absPath) {
  global $dPconfig;
  $mbPath = $dPconfig["root_dir"];
  
  $absPath = strtolower(strtr($absPath, "\\", "/"));
  $mbPath = strtolower(strtr($mbPath, "\\", "/"));
  
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
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function mbTrace($var, $label = null, $log = false) {
  return processLog(print_r($var, true), $label, $log);
}

/**
 * Traces variable using preformated text prefixed with a label
 * @return string|int The processed log or the size of the data written in the log file 
 **/
function mbExport($var, $label = null, $log = false) {
  return processLog(var_export($var, true), $label, $log);
}

function print_infos($var, $name = '') {
	if (count($var))
	  return "\n<pre><a href='#1' onclick='var s=this.parentNode.childNodes[1].style;s.display=s.display==\"none\"?\"\":\"none\";return false;'>$name</a><span style='display:none;'> " . 
             substr(print_r($var, true), 6) . '</span></pre>';
}

/**
 * Custom herror handler with backtrace
 * @return null
 */
function errorHandler($errorCode, $errorText, $errorFile, $errorLine) {
  global $divClasses, $errorTypes, $errorCategories, $AppUI, $performance;
  
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
  $contexts = debug_backtrace();
  foreach($contexts as &$ctx) {
    unset($ctx['args']);
    unset($ctx['object']);
  }
  $hash = md5($errorCode.$errorText.$errorFile.$errorLine.serialize($contexts));
  
  array_shift($contexts);
  $log = "\n\n<div class='$divClass' title='$hash'>";
  
  if ($AppUI && $AppUI->user_id){
    $log .= "\n<strong>User: </strong>$AppUI->user_first_name $AppUI->user_last_name ($AppUI->user_id)";
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
  global $divClasses, $errorTypes, $errorCategories, $AppUI, $performance;
  
  $divClass = "big-warning";
  
  // Contextes 
  $contexts = $exception->getTrace();
  foreach($contexts as &$ctx) {
    unset($ctx['args']);
  }
  $hash = md5(serialize($contexts));
  
  $log = "\n\n<div class='$divClass' title='$hash'>";
  
  if ($AppUI && $AppUI->user_id) {
    $log .= "\n<strong>User: </strong>$AppUI->user_first_name $AppUI->user_last_name ($AppUI->user_id)";
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
