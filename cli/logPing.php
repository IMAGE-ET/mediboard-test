<?php

require_once("utils.php");

function logPing($hostname, $fileLog, $dirLog) {

	$currentDir = dirname(__FILE__);

	announce_script("Ping logger");
	
	// Make the log line
	$dt = date('Y-m-d\TH:i:s');
	
	if ($hostname === "") {
		
		$hostname = "localhost";
	}
	
	$ping = shell_exec("ping " . $hostname . " -c 4 | tr -s '\n' | tail -n 1");

	if (check_errs($ping, NULL, "Failed to ping", "Ping successful!")) {
		
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
?>
