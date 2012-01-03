<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require dirname(__FILE__)."/mllp_utils.php";

// Ignores user logout
ignore_user_abort(true);
set_time_limit(0);

global $exit_status, $pid_file, $handler;
$exit_status = 1;

function on_shutdown() {
  global $exit_status, $pid_file, $handler;
  
  if ($exit_status == 1) { // Error
    outln("Server stopped unexpectedly");
    socket_close($handler->server->__socket);
    
    // restart the server
    $cmd = $_SERVER["_"]." ".implode(" ", $_SERVER["argv"]);
    echo exec($cmd);
  }
  else {
    outln("Server stopped normally");
    unlink($pid_file);
  }
}

function quit($new_exit_status){
  global $exit_status;
  $exit_status = $new_exit_status;
  exit($exit_status);
}

// ---- Read arguments
$argv = $_SERVER["argv"];
$argc = $_SERVER["argc"];

if (count($argv) < 4) {
  echo <<<EOT
Usage: {$argv[0]} <root_url> <username> <password> [--port port]
  <root_url>      The root url for mediboard, ie https://localhost/mediboard
  <username>      The name of the user requesting, ie cron
  <password>      The password of the user requesting, ie ****
  [--port <port>] The port to listen on

EOT;
  exit(0);
}

$options = array(
  "url"      => $argv[1],
  "username" => $argv[2],
  "password" => $argv[3],
  "debug"    => false,
  "port"     => 7001,
);

for($i = 3; $i < $argc; $i++) {
  switch($argv[$i]){
    case "--debug":
      $options["debug"] = true;
    break;
    
    case "--port":
      $options["port"] = $argv[++$i];
    break;
  }
}
// ---- End read arguments

register_shutdown_function("on_shutdown");

// Write a flag file with the PID and the port
$pid_file = "$tmp_dir/pid.".getmypid();
file_put_contents($pid_file, $options["port"]);

try {
  outln("Starting MLLP Socket handler on port ".$options["port"]." with user '".$options["username"]."'");
  
  $handler = new CMLLPSocketHandler($options["url"], $options["username"], $options["password"], $options["port"]);
  $handler->run();
  
  outln("MLLP Socket handler stopped");
  
  quit(0);
}
catch(Exception $e) {
  $message = $e->getMessage();
  
  if ($message == "Address already in use") {
    echo "$message\n";
    quit(0);
  }
  
  $stderr = fopen("php://stderr", "w");
  fwrite($stderr, $message.PHP_EOL);
}

exit($exit_status);
