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



?>
