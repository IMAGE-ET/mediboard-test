<?php
require_once ("utils.php");
require_once ("Procedure.class.php");

function request($rootURL, $username, $password, $params, $times, $delay, $file) {
    
    $currentDir = dirname(__FILE__);
    
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
	
	$url = $rootURL . "/index.php?" . $login . "&" . $username . "&" . $password . "&" . $params;
	
	// Make mediboard path
	$MEDIBOARDPATH = "/var/log/mediboard";
	force_dir($MEDIBOARDPATH);
	
	$log = $MEDIBOARDPATH . "/jobs.log";
	force_file($log);
	
	if ($times > 1) {
		
		while ($times > 0) {
			
			$times--;
			mediboard_request($url, $log, $file, $times, $delay);
			sleep($delay);
		}
	}
	else {
		
		mediboard_request($url, $log, $file, $times, $delay);
	}
}

function mediboard_request($url, $log, $file, $times, $delay) {
	
	if ($file === "") {
		
		exec("wget \"" . $url . "\" --append-output=" . $log . " --force-directories --no-check-certificate", $request, $return_var);
		check_errs($return_var, true, "Failed to request to Mediboard", "Mediboard requested!");
		echo "Requested URL: " . $url . "\n";
	}
	else {
		
		exec("wget \"" . $url . "\" --append-output=" . $log . " --force-directories --no-check-certificate -O " . $file, $request, $return_var);
		check_errs($return_var, true, "Failed to request to Mediboard", "Mediboard requested!");
		echo "Requested URL: " . $url . "\n";
	}
}

function requestProcedure( $backMenu ) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice( $choice );
  
  $qt_rootURL  = $procedure->createQuestion( "Root URL (ie https://localhost/mediboard): " );
  $rootURL     = $procedure->askQuestion( $qt_rootURL );
  
  if ( $rootURL === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu( $backMenu, true );
    exit();
  }
  
  $qt_username  = $procedure->createQuestion( "Username (ie cron): " );
  $username     = $procedure->askQuestion( $qt_username );
  
  $password = prompt_silent();
  
  $qt_params  = $procedure->createQuestion( "Params (ie m=dPpatients&tab=vw_medecins): " );
  $params     = $procedure->askQuestion( $qt_params );
  
  $qt_times    = $procedure->createQuestion( "Times (number of repetitions) [default 1]: ", 1 );
  $times       = $procedure->askQuestion( $qt_times );
  
  $qt_delay    = $procedure->createQuestion( "Delay (time between each repetition) [default 1]: ", 1 );
  $delay       = $procedure->askQuestion( $qt_delay );
  
  $qt_file    = $procedure->createQuestion( "File (file for the output, ie log.txt) [default no file]: ");
  $file       = $procedure->askQuestion( $qt_file );
  
  echo "\n";
  request($rootURL, $username, $password, $params, $times, $delay, $file);
}

function requestCall( $command, $argv ) {
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
?>