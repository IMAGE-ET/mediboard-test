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
 * Log the Uptime of a server
 * 
 * @param string $file     Target to log
 * @param string $hostname Hostname for uptime
 * 
 * @return None 
 */
function logUptime($file, $hostname) {

  $currentDir = dirname(__FILE__);

  announce_script("Uptime logger");

  // Make the log line
  $dt = date('Y-m-d\TH:i:s');
  
  if (($hostname == "localhost") || ($hostname == "")) {
    $up = shell_exec(
      "uptime | sed 's/\(.*\): \([0-9.]*\)[,]* \([0-9.]*\)[,]* \([0-9.]*\)/1mn:\\2\\t5mn:\\3\\t15mn:\\4/'"
    );
  }
  else {
    $up = shell_exec(
      "ssh ".$hostname.
      " uptime | sed 's/\(.*\): \([0-9.]*\)[,]* \([0-9.]*\)[,]* \([0-9.]*\)/1mn:\\2\\t5mn:\\3\\t15mn:\\4/'"
    );
  }
  
  if (check_errs($up, null, "Failed to get uptime", "Uptime successful!")) {
    // Log the line
    if ($file === "") {
      $file = "/var/log/uptime.log";
    }
    
    $fic = fopen($file, "a+");
    if (check_errs($fic, false, "Failed to open log file", "Log file opened at " . $file . "!")) {
      fwrite($fic, $hostname.": ".$dt." ".$up);
      fclose($fic);
    }
  }
}

/**
 * The Procedure for the loguptime function
 * 
 * @param object $backMenu The Menu for return
 * 
 * @return None
 */
function logUptimeProcedure( $backMenu ) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_hostname  = $procedure->createQuestion("Hostname [default localhost]: ", "localhost");
  $hostname     = $procedure->askQuestion($qt_hostname);
  
  if ( $hostname === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu($backMenu, true);
    exit();
  }
  
  $qt_file = $procedure->createQuestion(
    "File (target for log) [default /var/log/uptime.log]: ", "/var/log/uptime.log"
  );
  $file    = $procedure->askQuestion($qt_file);
  
  echo "\n";
  logUptime($file, $hostname);
}

/**
 * Function to use loguptime in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function logUptimeCall( $command, $argv ) {
  if (count($argv) == 2) {
    $file     = $argv[0];
    $hostname = $argv[1];
    
    logUptime($file, $hostname);
    return 0;
  }
  else {
    echo "\nUsage : $command loguptime [<file>] [<hostname>]\n
<file>        : target for log, default /var/log/uptime.log\n
Options :
[<hostname>]  : hostname for uptime, default localhost\n\n";
    return 1;
  }
}
?>
