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
 * Log a Ping request
 * 
 * @param string $hostname Host to ping
 * @param string $fileLog  Filename for the output
 * @param string $dirLog   Directory where to create the filelog
 * 
 * @return None
 */
function logPing($hostname, $fileLog, $dirLog) {
  $currentDir = dirname(__FILE__);
  announce_script("Ping logger");
  
  // Make the log line
  $dt = date('Y-m-d\TH:i:s');
  
  if ($hostname === "") {
    $hostname = "localhost";
  }
  
  $ping = shell_exec("ping " . $hostname . " -c 4 | tr -s '\n' | tail -n 1");

  if (check_errs($ping, null, "Failed to ping", "Ping successful!")) {
    // Log the line
    if ($dirLog === "") {
      $dirLog = "/var/log/ping";
    }
    
    if ($fileLog === "") {
      $file=$dirLog . "/" . $hostname . ".log";
    }
    else {
      $file = $dirLog . "/" . $fileLog;
    }
    
    force_dir($dirLog);
  
    $fic = fopen($file, "a+");
    if (check_errs($fic, false, "Failed to open log file", "Log file opened at " . $file . "!")) {
      fwrite($fic, $dt . " " . $ping);
      fclose($fic);
    }
  }
}

/**
 * The Procedure for the logping function
 * 
 * @param object $backMenu The Menu for return
 * 
 * @return None
 */
function logPingProcedure($backMenu) {
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
  
  $qt_dir       = $procedure->createQuestion("File directory [default /var/log/ping]: ", "/var/log/ping");
  $dir          = $procedure->askQuestion($qt_dir);
  
  $qt_output    = $procedure->createQuestion("Output file [default $hostname.log]: ", "$hostname.log");
  $output       = $procedure->askQuestion($qt_output);
  
  echo "\n";
  logPing($hostname, $output, $dir);
}

/**
 * Function to use logping in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function logPingCall($command, $argv) {
  if (count($argv) == 3) {
    $hostname = $argv[0];
    $fileLog  = $argv[1];
    $dirLog   = $argv[2];
    
    logPing($hostname, $fileLog, $dirLog);
    
    return 0;
  }
  else {
    echo "\nUsage : $command logping <host> [<output_filename>] [<directory>]\n
<host>              : target to ping\n
Options :
[<output_filename>] : filename for the output, default <host>.log
[<directory>]       : directory where to create <output_filename>, default /var/log/ping\n\n";

    return 1;
  }
}
?>
