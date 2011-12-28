<?php

// CLI or die
PHP_SAPI === "cli" or die;

// Ignores user logout
ignore_user_abort(true);
set_time_limit(0);

require dirname(__FILE__)."/../classes/CMLLPSocketHandler.class.php";

// ---- Read arguments
$argv = $_SERVER["argv"];
$argc = $_SERVER["argc"];

if (count($argv) < 5) {
  echo <<<EOT
Usage: {$argv[0]} <root_url> <username> <password> "<params>" [--port port]
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

try {
  $stdout = fopen("php://stdout", "w");
  fwrite($stdout, "Starting MLLP Socket handler".PHP_EOL);
  
  $handler = new CMLLPSocketHandler($options["url"], $options["username"], $options["password"], $options["port"]);
  $handler->run();
  
  fwrite($stdout, "MLLP Socket handler stopped".PHP_EOL);
  
  exit(0);
}
catch(Exception $e) {
  $stderr = fopen("php://stderr", "w");
  fwrite($stderr, $e->getMessage().PHP_EOL);
  
  exit(1);
}
