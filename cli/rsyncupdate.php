<?php

require_once("utils.php");
require_once("update.php");

function rsyncupdate($action, $revision) {
	
	/*if (!(function_exists("ssh2_connect"))) {
		
		cecho("PECL module SSH2 required\n", "red", "bold");
		exit();
	}*/

	$currentDir = dirname(__FILE__);

	announce_script("Mediboard SVN updater and rsyncer");

	if ($revision === "") {
		
		$revision = "HEAD";
	}

	// Choose the target revision
	switch($action) {

		case "info":

			update("info", $revision);
			break;

		case "real":

			update("real", $revision);
			break;
	}
	
	// File must exists (touch doesn't override)
	touch("rsyncupdate.exclude");
	
	if ($action != "info") {
		
		// Rsyncing -- Parsing rsyncupdate.conf
		$lines = file($currentDir . "/rsyncupdate.conf");
			
		foreach ($lines as $line_num => $line) {
		    
			// Skip comment lines and empty lines
			if ((trim(substr($line, 0, 1)) != "#") && (trim(substr($line, 0, 1)) != "")) {
				
				$line = trim($line);
				echo "-- Rsync " . $line . " --\n";
				
				$usernamePOS = strpos($line, "@");
				
				if ($usernamePOS) {
					
					$hostnamePOS = strpos($line, ":", $usernamePOS);
					
					if ($hostnamePOS) {
						
						$username = substr($line, 0, $usernamePOS);
						$hostname = substr($line, $usernamePOS+1, $hostnamePOS-($usernamePOS+1));
					}
				}
				
				if ($usernamePOS == false) {
					
					// Local folder
					$dirName = $line;
	
					$rsync = shell_exec("rsync -avpz --stats " . $currentDir . "/.. --delete " . $line . " --exclude-from=" . $currentDir . "/rsyncupdate.exclude --exclude includes/config_overload.php --exclude tmp --exclude lib --exclude files --exclude includes/config.php --exclude images/pictures/logo_custom.png");
				
					echo $rsync . "\n";
				
					check_errs($rsync, NULL, "Failed to rsync " . $line, "Successfully rsync-ed " . $line);
	
					// Test for same files
					if (realpath($currentDir . "/../tmp/svnlog.txt") != realpath($dirName . "/tmp/svnlog.txt")) {
						
						copy($currentDir . "/../tmp/svnlog.txt", $dirName . "/tmp/svnlog.txt");
					}
	
					// Test for same files
					if (realpath($currentDir . "/../tmp/svnstatus.txt") != realpath($dirName . "/tmp/svnstatus.txt")) {
						
						copy($currentDir . "/../tmp/svnstatus.txt", $dirName . "/tmp/svnstatus.txt");
					}							
				}
				else {
					
					$dirName = substr($line, $hostnamePOS+1);
					
					$rsync = shell_exec("rsync -avpz --stats " . $currentDir . "/.. --delete " . $line . " --exclude-from=" . $currentDir . "/rsyncupdate.exclude --exclude includes/config_overload.php --exclude tmp --exclude lib --exclude files --exclude includes/config.php --exclude images/pictures/logo_custom.png");
				
					echo $rsync . "\n";
				
					check_errs($rsync, NULL, "Failed to rsync " . $line, "Successfully rsync-ed " . $line);
					
					$scp = shell_exec("scp " . $currentDir . "/../tmp/svnlog.txt " . $line . "/tmp/svnlog.txt");
					$scp = shell_exec("scp " . $currentDir . "/../tmp/svnstatus.txt " . $line . "/tmp/svnstatus.txt");
					
					/*$connection = ssh2_connect($hostname);
					if ($connection) {
						
						$home = getenv("HOME");
						
						if (ssh2_auth_pubkey_file($connection, $username, $home . "/.ssh/id_rsa.pub", $home . "/.ssh/id_rsa")) {
							
							ssh2_scp_send($connection, $currentDir . "/../tmp/svnlog.txt", $dirName . "/tmp/svnlog.txt");
							ssh2_scp_send($connection, $currentDir . "/../tmp/svnstatus.txt", $dirName . "/tmp/svnstatus.txt");
						}
					}*/
				}
			}
		}
	}
}
?>