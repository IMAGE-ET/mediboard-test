<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $performance, $dPconfig;
$performance["error"] = 0;
$performance["warning"] = 0;
$performance["notice"] = 0;
$logPath = $dPconfig["root_dir"]."/tmp/mb-log.html";

// Do not set to E_STRICT as it hides fatal errors to our error handler
// Strict warning will still be handle by our handler anyway

error_reporting(E_ALL);
ini_set("error_log", $logPath);
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
 * Traces variable using preformated text prefixed with a label
 * @return void 
 **/
function mbTrace($var, $label = null, $log = false) {
  $export = print_r($var, true);
//  $export = var_export($var, true); 
  $export = htmlspecialchars($export);
  $errorTime = date("Y-m-d H:i:s");
  
  $msg = "<pre>[$errorTime] $label: $export</pre>";
  
  if ($log) {
    global $logPath;
    file_put_contents($logPath, $msg, FILE_APPEND);
  } else {
    echo $msg;
  }
  
}

/**
 * Traces variable using preformated text prefixed with a label
 * @return void 
 **/
function mbExport($var, $label = null, $log = false) {
  $export = var_export($var, true); 
  $export = htmlspecialchars($export);
  $errorTime = date("Y-m-d H:i:s");
  
  $msg = "<pre>[$errorTime] $label: $export</pre>";
  
  if ($log) {
    global $logPath;
    file_put_contents($logPath, $msg, FILE_APPEND);
  } else {
    echo $msg;
  }
  
}

/**
 * Custom herror handler with backtrace
 * @return null
 */
function errorHandler($errno, $errstr, $errfile, $errline) {
  global $divClasses, $errorTypes, $errorCategories, $logPath, $AppUI, $performance;
  
// See ALL errors
//  echo "<br />[$errno] : $errstr, $errfile : $errline";
  
  // Handles the @ case
  if (!error_reporting()) {
    return;
  }
  
  if (!array_key_exists($errno, $divClasses)) {
    return;
  }
  
  $errorTime = date("Y-m-d H:i:s");
  
  // CMbArray non chargé
  $divClass = isset($divClasses[$errno]) ? $divClasses[$errno] : null;
  $errorType = isset($errorTypes[$errno]) ? $errorTypes[$errno] : null;
  
  
  $log = "\n\n<div class='$divClass'>";
  
  if ($AppUI && $AppUI->user_id){
    $log .= "\n<strong>User: </strong>$AppUI->user_first_name $AppUI->user_last_name ($AppUI->user_id)";
  }
  
  $log .= "\n<strong>Query: </strong>" . @$_SERVER["QUERY_STRING"];
  
  $log .= "<br />";
  
  // Erreur générale
  $errfile = mbRelativePath($errfile);
  $log .= "\n<strong>Time: </strong>$errorTime";
  $log .= "\n<strong>Type: </strong>$errorType";
  $log .= "\n<strong>Text: </strong>$errstr";
  $log .= "\n<strong>File: </strong>$errfile";
  $log .= "\n<strong>Line: </strong>$errline";
  $log .= "<hr />";
  
  // Contextes 
  $contexts = debug_backtrace();
  array_shift($contexts);
  foreach($contexts as $context) {
    $function = isset($context["class"]) ? $context["class"] . ":" : "";
    $function.= $context["function"] . "()";
    
    $log .= "\n<strong>Function: </strong>" . $function;
    
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
  
  $performance[$errorCategories[$errno]]++;
  
  if (ini_get("log_errors")) {
    file_put_contents($logPath, $log, FILE_APPEND);
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
  global $divClasses, $errorTypes, $errorCategories, $logPath, $AppUI, $performance;
  
  $divClass = "big-warning";
  
  $log = "\n\n<div class='$divClass'>";
  
  if ($AppUI && $AppUI->user_id) {
    $log .= "\n<strong>User: </strong>$AppUI->user_first_name $AppUI->user_last_name ($AppUI->user_id)";
  }
  
  $log .= "\n<strong>Query: </strong>" . @$_SERVER["QUERY_STRING"];
  
  $log .= "<br />";
  
  // Erreur générale
  $errorTime = date("Y-m-d H:i:s");
  $errorType = "Exception";
  $errorFile = mbRelativePath($exception->getFile());
  $errorLine = $exception->getLine();
  $errorText = $exception->getMessage();
  $log .= "\n<strong>Time: </strong>$errorTime";
  $log .= "\n<strong>Type: </strong>$errorType";
  $log .= "\n<strong>Text: </strong>$errorText";
  $log .= "\n<strong>File: </strong>$errorFile";
  $log .= "\n<strong>Line: </strong>$errorLine";
  $log .= "<hr />";
  
  // Contextes 
  $contexts = $exception->getTrace();
  foreach($contexts as $context) {
    $function = isset($context["class"]) ? $context["class"] . ":" : "";
    $function.= $context["function"] . "()";
    
    $log .= "\n<strong>Function: </strong>" . $function;
    
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
    file_put_contents($logPath, $log, FILE_APPEND);
  }
  
  if (ini_get("display_errors")) {
    echo $log;
  }
} 

set_exception_handler("exceptionHandler");

// Initialize custom error handler
if (!is_file($logPath)) {
  $initTime = date("Y-m-d H:i:s");
  $logInit = "<h2>Log de Mediboard ré-initialisé depuis $initTime</h2>";
  file_put_contents($logPath, $logInit);
}
?>
