<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision$
 * @author Thomas Despoix
 */

$logPath = "tmp/mb-log.html";

error_reporting( E_ALL );
ini_set("error_log", $logPath);
ini_set("log_errors_max_len", "4M");
ini_set("log_errors", true);
ini_set("display_errors", $dPconfig["debug"]);

$divClasses = array (
  E_ERROR => "big-error",
  E_WARNING => "big-warning",
  E_NOTICE => "big-info",
  E_PARSE => "big-info",
  E_CORE_ERROR => "big-error",
  E_CORE_WARNING => "big-warning",
  E_COMPILE_ERROR => "big-error",
  E_COMPILE_WARNING => "big-warning",
  E_USER_ERROR => "big-error",
  E_USER_WARNING => "big-warning",
  E_USER_NOTICE => "big-info",
);

$errorTypes = array (
  E_ERROR => "Error",
  E_WARNING => "Warning",
  E_NOTICE => "Notice",
  E_PARSE => "Parse",
  E_CORE_ERROR => "Core error",
  E_CORE_WARNING => "Core warning",
  E_COMPILE_ERROR => "Compile error",
  E_COMPILE_WARNING => "Compile warning",
  E_USER_ERROR => "User error",
  E_USER_WARNING => "User warning",
  E_USER_NOTICE => "User notice",
);

// To be put in mbFonctions
function mbRelativePath($absPath) {
  global $dPconfig;
  $mbPath = $dPconfig["root_dir"];
  
  // Hack for MS Windows server
  $absPath = strtr($absPath, "\\", "/");
  
  $relPath = strpos($absPath, $mbPath) === 0 ? 
    substr($absPath, strlen($mbPath) + 1) :
    $mbPath;
    
  return $relPath;
}

function errorHandler($errno, $errstr, $errfile, $errline) {
  global $divClasses;
  global $errorTypes;
  global $logPath;
  
  // Handles the @ case
  if (!error_reporting()) {
    return;
  }
   
  if (!array_key_exists($errno, $divClasses)) {
    return;
  }
  
  $errorTime = date("Y-m-d H:i:s");
  
  $divClass = @$divClasses[$errno];
  $errorType = @$errorTypes[$errno];
  
  $log = "\n\n<div class='$divClass'>";
  $log .= "\n<strong>Time: </strong>$errorTime";
  $log .= "\n<strong>Type: </strong>$errorType";
  $log .= "\n<strong>Text: </strong>$errstr";
  $log .= "\n<strong>File: </strong>" . mbRelativePath($errfile);
  $log .= "\n<strong>Line: </strong>$errline";
  $log .= "<hr />";
  
  $contexts = debug_backtrace();
  array_shift($contexts);
  foreach($contexts as $context) {
    $log .= "\n<strong>Function: </strong>" . $context["function"];
    if (isset($context["class"])) {
      $log .= "\n<strong>Class: </strong>" . $context["class"];
    }
    $log .= "\n<strong>File: </strong>" . mbRelativePath($context["file"]);
    $log .= "\n<strong>Line: </strong>" . $context["line"];
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


// Initialize custom error handler
if (!@filesize($logPath)) {
  $initTime = date("Y-m-d H:i:s");
  
  $logInit = "<link rel='stylesheet' type='text/css' href='style/mediboard/main.css?build=24' media='all' />";  
  $logInit .= "<h2>Log de Mediboard ré-initailisé depuis $initTime</h2>";
  file_put_contents($logPath, $logInit);
}

set_error_handler("errorHandler");
?>
