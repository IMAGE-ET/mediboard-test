<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once "utils.php";

global $argv;

$hostname = "";
$username = "";
$file = "";
$help = false;
$i = 0;
$command = array_shift($argv);

if (count($argv) >= 2 && count($argv) <= 4) {
  foreach ($argv as $key=>$arg) {
    switch ($arg) {
      case "-h":
        $help = true;
        break;
        
      default:
        switch ($i) {
          case 0:
            $hostname = $arg;
            break;
            
          case 1:
            $username = $arg;
            break;
            
          case 2:
            $file = $arg;
            break;
        }
        
        $i++;
    }
  }
}
else {
  $help = true;
}

echo chr(27)."[1m--- Synchronize files (".date("l d F H:i:s").") ---".chr(27)."[0m"."\n";

if ($help) {
  echo "Usage : ".basename($command)." <hostname> <username> <password> <file>
<hostname> : host to connect
<username> : username requesting
<file>     : file to get or push\n";

  return;
}

// Create the temporary directory
if (!(is_file("/tmp/synConfig"))) {
  if (!(is_dir("/tmp/synConfig"))) {
    if (!(check_errs(
      mkdir("/tmp/synConfig", 0, true),
      false,
      "Unable to create temporay directory.",
      "Temporary directory created!"
    ))) {
      return;
    }
  }
}
else {
  cecho("Error, /tmp/synConfig directory cannot be created.", "red");
  return;
}

exec("scp -p ".$username."@".$hostname.":".$file." /tmp/synConfig".basename($file), $result, $returnVar);
if (!(check_errs($returnVar, true, "Unable to get the file.", "File received!"))) {
  return;
}

if (!(check_errs(is_readable($file), false, $file." is not readable.", $file." is readable!"))) {
  return;
}

// If files have not the same last modification time
if (filemtime("/tmp/synConfig".basename($file)) != filemtime($file)) {
  // If remote file is younger, we get
  if (filemtime("/tmp/synConfig".basename($file)) > filemtime($file)) {
    echo "Remote file is younger. Older will be replaced.\n";
    
    if (!(check_errs(
      rename("/tmp/synConfig".basename($file), $file),
      false,
      "Unable to replace the file.",
      "The file has been replaced!"
    ))) {
      return;
    }
    
    // Set the owner group to APACHE_GROUP
    $APACHE_USER = shell_exec("ps -ef|grep apache|head -2|tail -1|cut -d' ' -f1");
    $APACHE_GROUP = shell_exec("groups ".$APACHE_USER." | cut -d' ' -f3");
    exec("chgrp ".$APACHE_GROUP." ".$file, $result, $returnVar);
    
    if (!(check_errs(
      $returnVar,
      true,
      "Unable to change owner group of ".$file.".",
      "Owner group of ".$file." set to ".$APACHE_GROUP."!"
    ))) {
      return;
    }
    
    // Set group permissions to file
    exec("chmod g+w ".$file, $result, $returnVar);
    
    if (!(check_errs(
      $returnVar,
      true,
      "Unable to change permissions of ".$file.".",
      "Permissions of ".$file." changed!"
    ))) {
      return;
    }
  }
  // Else, we push
  else {
    echo "Remote file is older. It will be replaced.\n";
    exec("scp ".$file." ".$username."@".$hostname.":".$file, $result, $returnVar);
    
    if (!(check_errs($returnVar, true, "Unable to push the file.", "File sent!"))) {
      return;
    }
  }
}
else {
  echo "Files have the same last modification time.\n";
  
  return;
}
?>