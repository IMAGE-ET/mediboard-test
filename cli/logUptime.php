<?php

require_once("utils.php");

function logUptime($file, $hostname) {

	$currentDir = dirname(__FILE__);

	announce_script("Uptime logger");

	// Make the log line
	$dt = date('Y-m-d\TH:i:s');
	
	if ($hostname === "") {
		
		$hostname = "localhost";
		$up = shell_exec("uptime | sed 's/\(.*\): \([0-9.]*\)[,]* \([0-9.]*\)[,]* \([0-9.]*\)/1mn:\\2\\t5mn:\\3\\t15mn:\\4/'");
	}
	else {
		
		$up = shell_exec("ssh " . $hostname . " uptime | sed 's/\(.*\): \([0-9.]*\)[,]* \([0-9.]*\)[,]* \([0-9.]*\)/1mn:\\2\\t5mn:\\3\\t15mn:\\4/'");
	}
	
	if (check_errs($up, NULL, "Failed to get uptime", "Uptime successful!")) {
		
		// Log the line
		
		if ($file === "") {
			
			$file = "/var/log/uptime.log";
		}
		
		$fic = fopen($file, "a+");
		if (check_errs($fic, false, "Failed to open log file", "Log file opened at " . $file . "!")) {
			
			fwrite($fic,$hostname . ": " . $dt . " " . $up);
		
			fclose($fic);
		}
	}
}
?>
