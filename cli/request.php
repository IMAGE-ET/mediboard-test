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
require_once dirname(__FILE__)."/classes/Procedure.class.php";

/**
 * Launcher to request Mediboard
 * 
 * @param string $rootURL  Host to request (ie, https://localhost/mediboard)
 * @param string $username Username requesting
 * @param string $password Password of the user
 * @param string $params   Parameters to send (ie, "m=dPpatients&tab=vw_medecins")
 * @param int    $times    How many repeats
 * @param int    $delay    Delay (in seconds) between repeats
 * @param string $file     Output file
 * 
 * @return void
 */
function request($rootURL, $username, $password, $params, $times, $delay, $file) {
  announce_script("Mediboard request launcher");
    
  if ($times === "") {
    $times = 1;
  }
  
  if ($delay === "") {
    $delay = 1;
  }
  
  $times = intval($times);
  $delay = intval($delay);
  
  $login = "login=1";
  $username = "username=" . $username;
  $password = "password=" . $password;
  
  $url = $rootURL."/index.php?".$login."&".$username."&".$password."&".$params;
  
  // Make mediboard path
  $MEDIBOARDPATH = "/var/log/mediboard";
  force_dir($MEDIBOARDPATH);
  
  $log = $MEDIBOARDPATH . "/jobs.log";
  force_file($log);
  
  if ($times > 1) {
    while ($times > 0) {
      $times--;
      mediboard_request($url, $log, $file);
      sleep($delay);
    }
  }
  else {
    mediboard_request($url, $log, $file);
  }
}

/**
 * The request function
 * 
 * @param string $url  URL to request
 * @param string $log  Log file
 * @param string $file Output file
 * 
 * @return void
 */
function mediboard_request($url, $log, $file) {
  if ($file === "") {
    exec(
      "wget \"".$url."\" --append-output=".$log." --force-directories --no-check-certificate",
      $request, $return_var
    );
    check_errs($return_var, true, "Failed to request to Mediboard", "Mediboard requested!");
    echo "Requested URL: " . $url . "\n";
  }
  else {
    exec(
      "wget \"".$url."\" --append-output=".$log." --force-directories --no-check-certificate -O ".$file,
      $request, $return_var
    );
    check_errs($return_var, true, "Failed to request to Mediboard", "Mediboard requested!");
    echo "Requested URL: " . $url . "\n";
  }
}

/**
 * The Procedure for the request function
 * 
 * @param Menu $backMenu The Menu for return
 * 
 * @return void
 */
function requestProcedure(Menu $backMenu) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_rootURL  = $procedure->createQuestion("Root URL (ie https://localhost/mediboard): ");
  $rootURL     = $procedure->askQuestion($qt_rootURL);
  
  if ( $rootURL === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu($backMenu, true);
    exit();
  }
  
  $qt_username  = $procedure->createQuestion("Username (ie cron): ");
  $username     = $procedure->askQuestion($qt_username);
  
  $password = prompt_silent();
  
  $qt_params  = $procedure->createQuestion("Params (ie m=dPpatients&tab=vw_medecins): ");
  $params     = $procedure->askQuestion($qt_params);
  
  $qt_times    = $procedure->createQuestion("Times (number of repetitions) [default 1]: ", 1);
  $times       = $procedure->askQuestion($qt_times);
  
  $qt_delay    = $procedure->createQuestion("Delay (time between each repetition) [default 1]: ", 1);
  $delay       = $procedure->askQuestion($qt_delay);
  
  $qt_file    = $procedure->createQuestion("File (file for the output, ie log.txt) [default no file]: ");
  $file       = $procedure->askQuestion($qt_file);
  
  echo "\n";
  request($rootURL, $username, $password, $params, $times, $delay, $file);
}

/**
 * Function to use request in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function requestCall($command, $argv) {
  if (count($argv) == 7) {
    $rootURL  = $argv[0];
    $username = $argv[1];
    $password = $argv[2];
    $params   = $argv[3];
    $times    = $argv[4];
    $delay    = $argv[5];
    $file     = $argv[6];
    
    request($rootURL, $username, $password, $params, $times, $delay, $file);
    
    return 0;
  }
  else {
    echo "\nUsage : $command request <rootURL> <username> <password> \"<params>\" [options below]\n
<rootURL>     : host to request (ie, https://localhost/mediboard)
<username>    : username requesting
<password>    : password of the user
\"<params>\"    : parameters to send (ie, \"m=dPpatients&tab=vw_medecins\")\n
Options :
[<times>]     : how many repeats, default 1
[<delay>]     : delay (in seconds) between repeats, default 1
[<file>]      : output file\n\n";

    return 1;
  }
}
