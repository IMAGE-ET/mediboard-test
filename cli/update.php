<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once "utils.php";
require_once dirname(__FILE__)."/classes/Procedure.class.php";

/**
 * @param array $messages
 * @param array $prefixes
 */
function filter_commit_messages($messages, $prefixes = array("erg", "fnc", "fct", "bug", "war", "edi", "sys", "svn")) {
  foreach ($messages as $_i => $_line) {
    if (!preg_match("/^(".implode("|", $prefixes).")/i", $_line)) {
      unset($messages[$_i]);
    }
  }

  return $messages;
}

/**
 * Update Mediboard
 * 
 * @param string $action   action to perform: info|real|noup
 * @param string $revision revision number you want to update to
 * 
 * @return void
 */
function update($action, $revision) {
  $currentDir = dirname(__FILE__);
  announce_script("Mediboard SVN updater");

  $MB_PATH = $currentDir . "/..";
  $log = $MB_PATH . "/tmp/svnlog.txt";
  $tmp = $MB_PATH . "/tmp/svnlog.tmp";
  $dif = $MB_PATH . "/tmp/svnlog.dif";
  $status = $MB_PATH . "/tmp/svnstatus.txt";
  $event = $MB_PATH . "/tmp/svnevent.txt";
  $prefixes = array("erg", "fnc", "fct", "bug", "war", "edi", "sys", "svn");

  if ($revision === "") {
    $revision = "HEAD";
  }

  // Choose the target revision
  switch($action) {
    case "info":
      $out = array();
      exec("svn info $MB_PATH", $out, $ret);
      if (check_errs($ret, null, "SVN info error", "SVN info successful!")) {
        echo $out[5]."\n";
      }

      $out = array();
      exec("svn log $MB_PATH -r BASE:$revision", $out, $ret);
      if (check_errs($ret, null, "SVN log error", "SVN log successful!")) {
        $out = filter_commit_messages($out);
        echo implode("\n", $out)."\n";
      }

      $out = array();
      exec("svn info $MB_PATH -r $revision", $out, $ret);
      if (check_errs($ret, null, "SVN info error", "SVN info successful!")) {
        echo $out[5]."\n";
      }
      break;

    case "real":
      // Concat the source (BASE) revision number : 5th line of SVN info (!)
      $out = array();
      exec("svn info $MB_PATH", $out, $ret);
      if (check_errs($ret, null, "Failed to get source revision info", "SVN Revision source info written!")) {
        $fic = fopen($tmp, "w");
        
        if (check_errs($fic, false, "Failed to open tmp file", "Tmp file opened!")) {
          fwrite($fic, $out[5]."\n");
          fclose($fic);
        }
      }
      
      // Concat SVN Log from BASE to target revision
      $out = array();
      exec("svn log $MB_PATH -r BASE:$revision", $out, $ret);
      if (check_errs($ret, null, "Failed to retrieve SVN log", "SVN log retrieved!")) {
        $fic = fopen($dif, "w");
        
        if (check_errs($fic, false, "Failed to open dif file", "Dif file opened!")) {
          fwrite($fic, implode("\n", $out) . "\n");
          fclose($fic);
          
          $fic = fopen($dif, "r");
          $fic2 = fopen($tmp, "a+");

          // TODO: use filter_commit_messages()
          while (!feof($fic)) {
            $buffer = fgets($fic);

            if (preg_match("/(erg:|fnc:|fct:|bug:|war:|edi:|sys:|svn:)/i", $buffer)) {
              fwrite($fic2, $buffer);
            }
          }
          
          fclose($fic);
          fclose($fic2);
          
          echo "SVN log parsed!\n";
          unlink($dif);
        }
      }
      
      // Perform actual update
      $out = array();
      exec("svn update $MB_PATH --revision $revision", $out, $ret);
      echo implode("\n", $out)."\n";
      check_errs($ret, null, "Failed to perform SVN update", "SVN update performed!");
      
      // Concat the target revision number
      $fic = fopen($tmp, "a+");
      $out = array();
      exec("svn info $MB_PATH", $out, $ret);
      if (check_errs($ret, null, "Failed to get target revision info", "SVN Revision target info written!")) {
        fwrite($fic, "\n" . implode("\n", $out));
        fclose($fic);
      }
      
      // Contact dating info
      $fic = fopen($tmp, "a+");
      fwrite($fic, "--- Updated Mediboard on " . strftime("%Y-%m-%d %H:%M:%S") . " ---\n");
      fclose($fic);
      
      // Concat tmp file to log file //
      // Ensure log file exists
      force_file($log);
      
      // Log file is reversed, make it straight
      shell_exec("tac " . $log . " > " . $log . ".straight");
      
      // Concat tmp file
      shell_exec("cat " . $tmp . " >> " . $log . ".straight");
      
      // Reverse the log file for user convenience
      shell_exec("tac " . $log . ".straight > " . $log);
      
      // Clean files
      unlink($log . ".straight");
      unlink($tmp);
      
      // Write status file
      $svn = shell_exec("svn info " . $MB_PATH . " | awk 'NR==5'");
      if (check_errs($svn, null, "Failed to write status file", "Status file written!")) {
        $fic = fopen($status, "w");
        fwrite($fic, $svn . "Date: " . strftime("%Y-%m-%d %H:%M:%S") . "\n");
        fclose($fic);
        
        if (file_exists($event)) {
          $fic = fopen($event, "a");
        }
        else {
          $fic = fopen($event, "w");
        }
        
        fwrite($fic, "#".date('Y-m-d H:i:s'));
        fwrite($fic, "\nMise a jour. ".$svn);
        fclose($fic);
      }
      break;
  }
}

/**
 * The Procedure for the update function
 * 
 * @param Menu $backMenu The Menu for return
 * 
 * @return void
 */
function updateProcedure(Menu $backMenu) {
  $procedure = new Procedure();
  
  echo "Action to perform:\n\n";
  echo "[1] Show the update log\n";
  echo "[2] Perform the actual update\n";
  echo "--------------------------------\n";
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_action = $procedure->createQuestion("\nSelected action: ");
  $action = $procedure->askQuestion($qt_action);
  
  switch ($action) {
    case "1":
      $action = "info";
      break;
      
    case "2":
      $action = "real";
      break;
      
    case $choice:
      $procedure->clearScreen();
      $procedure->showMenu($backMenu, true);
      
    default:
      $procedure->clearScreen();
      cecho("Incorrect input", "red");
      echo "\n";
      setupProcedure($backMenu);
  }
  
  $qt_revision = $procedure->createQuestion("\nRevision number [default HEAD]: ", "HEAD");
  $revision = $procedure->askQuestion($qt_revision);
  
  echo "\n";
  update($action, $revision);
}

/**
 * Function to use update in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function updateCall( $command, $argv ) {
  if (count($argv) == 2) {
    $action   = $argv[0];
    $revision = $argv[1];
    
    update($action, $revision);
    
    return 0;
  }
  else {
    echo "\nUsage : $command update <action> [<revision>]\n
<action>      : action to perform: info|real|noup
  info        : shows the update log, no rsync
  real        : performs the actual update and the rsync\n
Option:
[<revision>]  : revision number you want to update to, default HEAD\n\n";

    return 1;
  }
}
