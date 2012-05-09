<?php

require_once("utils.php");

function update($action, $revision) {

	$currentDir = dirname(__FILE__);

	announce_script("Mediboard SVN updater");

	$MB_PATH = $currentDir . "/..";
	$log = $MB_PATH . "/tmp/svnlog.txt";
	$tmp = $MB_PATH . "/tmp/svnlog.tmp";
	$dif = $MB_PATH . "/tmp/svnlog.dif";
	$status = $MB_PATH . "/tmp/svnstatus.txt";
	$prefixes = "erg|fnc|fct|bug|war|edi|sys|svn";

	if ($revision === "") {
		
		$revision = "HEAD";
	}

	// Choose the target revision
	switch($action) {

		case "info":

			$svn = shell_exec("svn info " . $MB_PATH . " | awk 'NR==5'");
			if (check_errs($svn, NULL, "SVN info error", "SVN info successful!")) {
				
				echo $svn . "\n";
			}

			$svn = shell_exec("svn log " . $MB_PATH . " -r BASE:" . $revision . " | grep -i -E '(" . $prefixes . ")'");
			if (check_errs($svn, NULL, "SVN log error", "SVN log successful!")) {
				
				echo $svn . "\n";
			}

			$svn = shell_exec("svn info " . $MB_PATH . " -r " . $revision . " | awk 'NR==5'");
			if (check_errs($svn, NULL, "SVN info error", "SVN info successful!")) {
				
				echo $svn . "\n";
			}

			break;

		case "real":

			// Concat the source (BASE) revision number : 5th line of SVN info (!)
			$svn = shell_exec("svn info " . $MB_PATH . " | awk 'NR==5'");
			if (check_errs($svn, NULL, "Failed to get source revision info", "SVN Revision source info written!")) {
				
				$fic = fopen($tmp, "w");
				if (check_errs($fic, false, "Failed to open tmp file", "Tmp file opened!")) {
					
					fwrite($fic, $svn . "\n");
					fclose($fic);
				}
			}
			
			// Concat SVN Log from BASE to target revision
    		$svn = shell_exec("svn log " . $MB_PATH . " -r BASE:" . $revision);
			if (check_errs($svn, NULL, "Failed to retrieve SVN log", "SVN log retrieved!")) {
				
				$fic = fopen($dif, "w");
				if (check_errs($fic, false, "Failed to open dif file", "Dif file opened!")) {
					
					fwrite($fic, $svn . "\n");
					fclose($fic);
					
					$fic = fopen($dif, "r");
					$fic2 = fopen($tmp, "a+");
					while (!feof($fic))
					{
						
						$buffer = fgets($fic);
						if (preg_match("/(erg:|fnc:|fct:|bug:|war:|edi:|sys:|svn:)/i", $buffer)) {
							
							fwrite($fic2, $buffer);
						}
					}
					fclose($fic);
					fclose($fic2);
					
					echo "SVN log parsed!\n";
					unlink($dif);
				}
			}
			
			// Perform actual update
			$svn = shell_exec("svn update " . $MB_PATH . " --revision " . $revision);
			echo $svn . "\n";
			check_errs($svn, NULL, "Failed to perform SVN update", "SVN update performed!");
			
			// Concat the target revision number
			$fic = fopen($tmp, "a+");
			$svn = shell_exec("svn info " . $MB_PATH . " | awk 'NR==5'");
			if (check_errs($svn, NULL, "Failed to get target revision info", "SVN Revision target info written!")) {
				
				fwrite($fic, "\n" . $svn);
				fclose($fic);
			}
			
			// Contact dating info
			$fic = fopen($tmp, "a+");
			fwrite($fic, "--- Updated Mediboard on " . date("l d F H:i:s") . " ---\n");
			fclose($fic);
			
			// Concat tmp file to log file //
			// Ensure log file exists
			force_file($log);
			
			// Log file is reversed, make it straight
			shell_exec("tac " . $log . " > " . $log . ".straight");
			
			// Concat tmp file
			shell_exec("cat " . $tmp . " >> " . $log . ".straight");
			
			// Reverse the log file for user convenience
			shell_exec("tac " . $log . ".straight > " . $log);
			
			// Clean files
			unlink($log . ".straight");
			unlink($tmp);
			
			// Write status file
			$svn = shell_exec("svn info " . $MB_PATH . " | awk 'NR==5'");
			if (check_errs($svn, NULL, "Failed to write status file", "Status file written!")) {
				
				$fic = fopen($status, "w");
				fwrite($fic, $svn . "Date: " . date("Y-m-d\TH:i:s") . "\n");
				fclose($fic);
			}
			
			break;
	}
}
?>
