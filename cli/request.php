<?php
require_once ("utils.php");

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

?>