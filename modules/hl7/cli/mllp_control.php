<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require dirname(__FILE__)."/mllp_utils.php";

// ---- Read arguments
$argv = $_SERVER["argv"];
$argc = $_SERVER["argc"];

if (count($argv) < 2) {
  echo <<<EOT
Usage: {$argv[0]} <command> [<port>]
  <port>    The port of the MLLP server to control
  <command> The command to issue on the MLLP server

EOT;
  exit(0);
}

@list($self, $command, $port) = $argv;
// ---- End read arguments

switch($command) {
  case "stop":
  //case "restart": 
    $client = new SocketClient();
    $client->connect("localhost", $port);
    $message = "__".strtoupper($command)."__\n";
    
    echo $client->sendandrecive($message);
    break;
    
  case "list": 
    if (PHP_OS != "WINNT") {
      echo "Not supported on Unix yet\n"; 
      exit(0);
    }

    $pid_files = glob("$tmp_dir/pid.*");
    $pids = array();
    foreach($pid_files as $_file) {
      $_pid = substr($_file, strrpos($_file, ".")+1);
      $pids[$_pid] = file_get_contents($_file);
    }
    
    exec("tasklist", $out);
    $out = array_slice($out, 2); 
    $processes = array();
    foreach($out as $_line) {
      $_pid = (int)substr($_line, 26, 8);
      $_ps_name = trim(substr($_line, 0, 25));
      $processes[$_pid] = $_ps_name;
    }
    
    echo "======  ========\n";
    echo "   PID  STATUS\n";
    echo "======  ========\n";
    foreach($pids as $_pid => $_port) {
      $_status = isset($processes[$_pid]) && stripos($processes[$_pid], "php") !== false;
      printf(" %5.d  %s\n", $_pid, $_status ? "OK" : "ERROR");
    }
    
    break;
    
  default:
    echo "Unknown command '$command'\n";
    exit(1);
}
