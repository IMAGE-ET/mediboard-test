<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once "style.php";

/**
 * Force directory creation
 * 
 * @param string $dir Directory name
 * 
 * @return void
 */
function force_dir($dir) {
  if (!(is_dir($dir))) {
    mkdir($dir, 0, true);
  }
}

/**
 * Check if error occurs
 * 
 * @param string $commandResult Return code of a command
 * @param string $failureCode   Specify the supposed failure code
 * @param string $failureText   Text to show if failure
 * @param string $successText   Text to show if success
 * 
 * @return bool
 */
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
  else if ($commandResult == $failureCode) {
    cecho("ERROR : " . $failureText, "red", "", "");
    echo "\n\n";
    
    return 0;
  }

  cecho($successText);
  echo "\n";
  
  return 1;
}

/**
 * Announce a script
 * 
 * @param string $scriptName Name of the script
 * 
 * @return void
 */
function announce_script($scriptName) {
  cecho(" --- " . $scriptName . " (" . date("l d F H:i:s") . ") ---", "white", "bold", "red");
  echo "\n";
}

/**
 * Print information about a script
 * 
 * @param string $info Text to print
 * 
 * @return void
 */
function info_script($info) {
  cecho(">>info: " . $info, "", "bold");
  echo "\n";
}

/**
 * Force file creation
 * 
 * @param string $file Filename
 * 
 * @return void
 */
function force_file($file) {
  if (!(file_exists($file))) {
    touch($file);
  }
}

/**
 * Prompt a question and get the response
 * 
 * @param string $ask     Question to ask for
 * @param string $default [optional] Default response
 * 
 * @return string
 */
function recup($ask, $default = null) {
  echo $ask;
  $answer = trim(fgets(STDIN));
  
  if ($default && $answer === "") {
    return $default;
  }
  
  return $answer;
}

// Not tested
/**
 * Not tested
 * 
 * @param string $libName Library name
 * @param string $url     URL where library can be found
 * @param string $version Version to get
 * 
 * @return void
 */
function package_lib($libName, $url, $version) {
  echo "Retrieve dompdf from " . $url . "\n";
  
  $svn = shell_exec("svn co " . $url . " tmp/" . $libName);
  if (check_errs($svn, null, "Failed to check out SVN", "SVN check out successful!")) {
    $svn = shell_exec(
      "tar cfz tmp/".$libName."-".$version.".tar.gz --directory ./tmp/ ".$libName." --exclude=.svn"
    );
    
    if (check_errs($svn, 1, "Failed to load package " . $libName, $libName . "packaged!")) {
      echo shell_exec("mv ./tmp/" . $libName . "-" . $version . ".tar.gz libpkg/") . "\n";
    }
  }
}

/**
 * Print a text with color, font style and background color
 * 
 * @param string $message    Text to print
 * @param string $color      [optional] Color of the text
 * @param string $style      [optional] Font style
 * @param string $background [optional] Background color
 * 
 * @return void
 */
function cecho($message, $color="default", $style="default", $background="default") {
  $text = "<c c=" . $color . " s=" . $style . " bg=" . $background . ">" . $message . "</c>";	
  echo parseShColorTag($text);
}

/**
 * In order to have a password prompt that works on many OS (works on Unix, Windows XP and Windows 2003 Server)
 * Source : http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
 * 
 * @param string $prompt [optional] Text to prompt
 * 
 * @return string
 */
function prompt_silent($prompt = "Enter Password:") {
  if (preg_match('/^win/i', PHP_OS)) {
    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    file_put_contents(
      $vbscript, 'wscript.echo(InputBox("'.addslashes($prompt).'", "", "password here"))'
    );
    $command = "cscript //nologo " . escapeshellarg($vbscript);
    $password = rtrim(shell_exec($command));
    unlink($vbscript);
        
    return $password;
  }
  else {
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
