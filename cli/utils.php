<?php

require_once("style.php");

function force_dir($dir) {
	
	if (!(is_dir($dir))) {
		
		mkdir($dir);
	}
}

function check_errs($commandResult, $failureCode, $failureText, $successText) {
	
	cecho(">> status: ", "", "bold", "");
	
	if (is_null($failureCode)) {

		if (is_null($commandResult)) {

			cecho("ERROR : " . $failureText, "red", "", "");
			echo "\n\n";
			return 0;
		}
		
		cecho($successText);
		echo "\n";
		return 1;
	}
	else {
		
		if ($commandResult == $failureCode) {

			cecho("ERROR : " . $failureText, "red", "", "");
			echo "\n\n";
			return 0;
		}

		cecho($successText);
		echo "\n";
		return 1;
	}
}

function announce_script($scriptName) {

	cecho(" --- " . $scriptName . " (" . date("l d F H:i:s") . ") ---", "white", "bold", "red");
	echo "\n";
}

function info_script($info) {

	cecho(">>info: " . $info, "", "bold");
	echo "\n";
}

function force_file($file) {

	if (!(file_exists($file))) {
		
		touch($file);
	}
}

// Not tested
function package_lib($libName, $url, $version) {

	echo "Retrieve dompdf from " . $url . "\n";
	
	$svn = shell_exec("svn co " . $url . " tmp/" . $libName);
	
	if (check_errs($svn, NULL, "Failed to check out SVN", "SVN check out successful!")) {
		
		$svn = shell_exec("tar cfz tmp/" . $libName . "-" . $version . ".tar.gz --directory ./tmp/ " . $libName . " --exclude=.svn");
		if (check_errs(1, "Failed to load package " . $libName, $libName . "packaged!")) {
			
			echo shell_exec("mv ./tmp/" . $libName . "-" . $version . ".tar.gz libpkg/") . "\n";
		}
	}
}

function cecho($message, $color="default", $style="default", $background="default") {
	
	$text = "<c c=" . $color . " s=" . $style . " bg=" . $background . ">" . $message . "</c>";	
	echo parseShColorTag($text);
}

// In order to have a password prompt that works on many OS (works on Unix, Windows XP and Windows 2003 Server)
// Source : http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
function prompt_silent($prompt = "Enter Password:") {
  if (preg_match('/^win/i', PHP_OS)) {
    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    file_put_contents(
          $vbscript, 'wscript.echo(InputBox("' . addslashes($prompt) . '", "", "password here"))');
        $command = "cscript //nologo " . escapeshellarg($vbscript);
        $password = rtrim(shell_exec($command));
        unlink($vbscript);
        return $password;
    } else {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");
            return;
        }
        $command = "/usr/bin/env bash -c 'read -s -p \"" . addslashes($prompt) . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        echo "\n";
        return $password;
    }
}

?>
