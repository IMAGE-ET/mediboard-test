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

$test_message = <<<EOT
MSH|^~\&||GA0000||VAERS PROCESSOR|20010331||ORU^R01|20010422GA03|D|2.3.1|||AL|
PID|||1234^^^^SR~1234-12^^^^LR~00725^^^^MR||Doe^John^Fitzgerald^JR^^^L||20001007|M||2106-3^White^HL70005|123 Peachtree St^APT 3B^Atlanta^GA^30210^^M^^GA067||(678) 555-1212^^PRN|
NK1|1|Jones^Jane^Lee^^RN|VAB^Vaccine administered by (Name)^HL70063|
NK1|2|Jones^Jane^Lee^^RN|FVP^Form completed by (Name)-Vaccine provider^HL70063|101 Main Street^^Atlanta^GA^38765^^O^^GA121||(404) 554-9097^^WPN|
ORC|CN|||||||||||1234567^Welby^Marcus^J^Jr^Dr.^MD^L|||||||||Peachtree Clinic|101 Main Street^^Atlanta^GA^38765^^O^^GA121|(404) 554-9097^^WPN|101 Main Street^^Atlanta^GA^38765^^O^^GA121
OBR|1|||^CDC VAERS-1 (FDA) Report|||20010316|
OBX|1|NM|21612-7^Reported Patient Age^LN||05|mo^month^ANSI|
OBX|1|TS|30947-6^Date form completed^LN||20010316|
OBX|2|FT|30948-4^Vaccination adverse events and treatment, if any^LN|1|fever of 106F, with vomiting, seizures, persistent crying lasting over 3 hours, loss of appetite|
OBX|3|CE|30949-2^Vaccination adverse event outcome^LN|1|E^required emergency room/doctor visit^NIP005|
OBX|4|CE|30949-2^Vaccination adverse event outcome^LN|1|H^required hospitalization^NIP005|
OBX|5|NM|30950-0^Number of days hospitalized due to vaccination adverse event^LN|1|02|d^day^ANSI|
OBX|6|CE|30951-8^Patient recovered^LN||Y^Yes^ HL70239|
OBX|7|TS|30952-6^Date of vaccination^LN||20010216|
OBX|8|TS|30953-4^Adverse event onset date and time^LN||200102180900|
OBX|9|FT|30954-2^Relevant diagnostic tests/lab data^LN||Electrolytes, CBC, Blood culture|
EOT;

function send($port, $message) {
  try {
    $client = new SocketClient();
    $client->connect("localhost", $port);
    echo $client->sendandrecive($message);
  }
  catch(Exception $e) {
    echo $e->getMessage()."\n";
  }
}

function get_ps_status(){
  global $tmp_dir;
  
  $pid_files = glob("$tmp_dir/pid.*");
  $processes = array();
  
  foreach($pid_files as $_file) {
    $_pid = substr($_file, strrpos($_file, ".")+1);
    $processes[$_pid] = array(
      "port" => file_get_contents($_file),
    );
  }
  
  if (PHP_OS == "WINNT") {
    exec("tasklist", $out);
    $out = array_slice($out, 2); 
    
    foreach($out as $_line) {
      $_pid = (int)substr($_line, 26, 8);
      if (!isset($processes[$_pid])) continue;
      
      $_ps_name = trim(substr($_line, 0, 25));
      $processes[$_pid]["ps_name"] = $_ps_name;
    }
  }
  else {
    exec("ps -e", $out);
    $out = array_slice($out, 1); 
    
    foreach($out as $_line) {
      $_pid = (int)substr($_line, 0, 5);
      if (!isset($processes[$_pid])) continue;
    
      $_ps_name = trim(substr($_line, 24));
      $processes[$_pid]["ps_name"] = $_ps_name;
    }
  }
  
  return $processes;
}

$msg_ok    = "OK    ";
$msg_error = "ERROR ";

if (PHP_OS != "WINNT") {
  $msg_ok    = "\033[1;32m$msg_ok\033[0m";
  $msg_error = "\033[1;31m$msg_error\033[0m";
}

switch($command) {
  case "stop":
  //case "restart": 
    send($port, "__".strtoupper($command)."__\n");
    break;
    
  case "test":
    send($port, "\x0B$test_message\x1C\x0D");
    break;
    
  case "list":
    $processes = get_ps_status();
    
    echo "--------------------------------------\n";
    echo "   PID |  PORT | STATUS | PS NAME     \n";
    echo "--------------------------------------\n";
    foreach($processes as $_pid => $_status) {
      $_ok = isset($_status["ps_name"]) && stripos($_status["ps_name"], "php") !== false;
      
      printf(" %5.d | %5.d | %s | %s \n", $_pid, $_status["port"], $_ok ? $msg_ok : $msg_error, $_ok ? $_status["ps_name"] : "");
    }
    
    break;
    
  default:
    echo "Unknown command '$command'\n";
    exit(1);
}
