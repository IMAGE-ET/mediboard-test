<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

require_once "utils.php";
require_once "update.php";
require_once dirname(__FILE__)."/classes/Procedure.class.php";

/**
 * Update Mediboard with RSYNC
 * 
 * @param string $action   Action to perform: info|real|noup
 * @param string $revision Revision number you want to update to
 * 
 * @return void
 */
function rsyncupdate($action, $revision) {
  $currentDir = dirname(__FILE__);
  announce_script("Mediboard SVN updater and rsyncer");
  
  if ($revision === "") {
    $revision = "HEAD";
  }
  
  // Choose the target revision
  switch ($action) {
    case "info":
      update("info", $revision);
      break;
      
    case "real":
      update("real", $revision);
      break;
  }
  
  // File must exists (touch doesn't override)
  touch("rsyncupdate.exclude");
  
  if ($action != "info") {
    // Rsyncing -- Parsing rsyncupdate.conf
    $lines = file($currentDir."/rsyncupdate.conf");
    
    foreach ($lines as $line_num=>$line) {
      // Skip comment lines and empty lines
      if ((trim(substr($line, 0, 1)) != "#") && (trim(substr($line, 0, 1)) != "")) {
        $line = trim($line);
        
        echo "Do you want to update ".$line." (y or n) [default n] ? ";
        $answer = trim(fgets(STDIN));
        
        if ($answer === "y") {
          echo "-- Rsync ".$line." --\n";
          $usernamePOS = strpos($line, "@");
          
          if ($usernamePOS) {
            $hostnamePOS = strpos($line, ":", $usernamePOS);
            
            if ($hostnamePOS) {
              $username = substr($line, 0, $usernamePOS);
              $hostname = substr($line, $usernamePOS + 1, $hostnamePOS - ($usernamePOS + 1));
            }
          }
          
          if ($usernamePOS == false) {
            // Local folder
            $dirName = $line;
            $rsync = shell_exec(
              "rsync -avpz --stats ".$currentDir."/.. --delete ".$line." --exclude-from=".
              $currentDir."/rsyncupdate.exclude".
              " --exclude includes/config_overload.php --exclude tmp --exclude lib --exclude files".
              " --exclude includes/config.php --exclude images/pictures/logo_custom.png"
            );
            echo $rsync."\n";
            
            check_errs($rsync, null, "Failed to rsync ".$line, "Successfully rsync-ed ".$line);
            
            // Test for same files
            if (realpath($currentDir."/../tmp/svnlog.txt") != realpath($dirName."/tmp/svnlog.txt")) {
              copy($currentDir."/../tmp/svnlog.txt", $dirName."/tmp/svnlog.txt");
            }
            
            // Test for same files
            if (realpath($currentDir."/../tmp/svnstatus.txt") != realpath($dirName."/tmp/svnstatus.txt")) {
              copy($currentDir."/../tmp/svnstatus.txt", $dirName."/tmp/svnstatus.txt");
            }
          }
          else {
            // Default value
            $port = 22;
            
            $portPOS = strpos($line, " ");
            
            if ($portPOS) {
              $dirName = substr($line, $hostnamePOS + 1, ($portPOS - ($hostnamePOS + 1)));
            }
            else {
              $dirName = substr($line, $hostnamePOS + 1);
            }
           
            if ($portPOS) {
              $port = substr($line, $portPOS + 1);
            }
            
            $rsync = shell_exec(
              "rsync -avpz --stats --rsh='ssh -p $port' ".$currentDir."/.. --delete ".substr($line, 0, $portPOS)." --exclude-from=".$currentDir.
              "/rsyncupdate.exclude --exclude includes/config_overload.php --exclude tmp".
              " --exclude lib --exclude files --exclude includes/config.php".
              " --exclude images/pictures/logo_custom.png"
            );
            
            echo $rsync."\n";
            
            check_errs($rsync, null, "Failed to rsync ".substr($line, 0, $portPOS), "Successfully rsync-ed ".substr($line, 0, $portPOS));
            
            $scp = shell_exec("scp -P ".$port." ".$currentDir."/../tmp/svnlog.txt ".substr($line, 0, $portPOS)."/tmp/svnlog.txt");
            $scp = shell_exec("scp -P ".$port." ".$currentDir."/../tmp/svnstatus.txt ".substr($line, 0, $portPOS)."/tmp/svnstatus.txt");
          }
        }
      }
    }
  }
}

/**
 * The Procedure for the rsyncupdate function
 * 
 * @param Menu $backMenu The Menu for return
 * 
 * @return void
 */
function rsyncUpdateProcedure(Menu $backMenu) {
  $procedure = new Procedure();
  
  echo "Action to perform:\n\n";
  echo "[1] Show the update log\n";
  echo "[2] Perform the actual update\n";
  echo "[3] No update, only rsync\n";
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
      
    case "3":
      $action = "noup";
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
  rsyncupdate($action, $revision);
}

/**
 * Function to use rsyncupdate in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function rsyncupdateCall( $command, $argv ) {
  if (count($argv) == 2) {
    $action   = $argv[0];
    $revision = $argv[1];
    
    rsyncupdate($action, $revision);
    
    return 0;
  }
  else {
    echo "\nUsage : $command rsyncupdate <action> [<revision>]\n
<action>      : action to perform: info|real|noup
  info        : shows the update log, no rsync
  real        : performs the actual update and the rsync
  noup        : no update, only rsync\n
Option:
[<revision>]  : revision number you want to update to, default HEAD\n\n";
      
    return 1;
  }
}
