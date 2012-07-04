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
require_once "Procedure.class.php";

/**
 * Setup Mediboard
 * 
 * @param object $subDir    modules|style
 * @param object $apacheGrp Name of the primary group for apache user
 * 
 * @return None
 */
function setup($subDir, $apacheGrp) {
  $currentDir = dirname(__FILE__);
  announce_script("Mediboard directories groups and mods");
  
  $darwin_kernel = PHP_OS;
  
  // To MAC
  if ($darwin_kernel == "Darwin") {
    $APACHE_USER = trim(shell_exec("ps -ef|grep httpd|head -2|tail -1|cut -d' ' -f4"));
    $APACHE_GROUP = trim(shell_exec("groups ".$APACHE_USER." | cut -d' ' -f1"));
  }
  // To Linux distributions
  else {
    $APACHE_USER = trim(shell_exec("ps -ef|grep apache|head -2|tail -1|cut -d' ' -f1"));
    $APACHE_GROUP = trim(shell_exec("groups ".$APACHE_USER." | cut -d' ' -f3"));
  }

  if ($apacheGrp != null) {
    $APACHE_GROUP = $apacheGrp;
  }
  
  $fic = fopen("/etc/group", "r");
  while (!feof($fic)) {
    $buffer = fgets($fic);
    
    if (preg_match("/^($APACHE_GROUP:)/m", $buffer)) {
      echo $APACHE_GROUP." group exists!\n";
      
      // Check optional sub-directory
      switch ($subDir) {
        case "modules":
          $BASE_PATH = "modules";
          break;
          
        case "style":
          $BASE_PATH = "style";
          break;
          
        default:
          $BASE_PATH = dirname($currentDir);
          $SUB_PATH = array(
            "lib/", "tmp/", "files/", "includes/", "modules/*/locales/", "modules/hprimxml/xsd/", "locales/*/"
          );
      }
      
      // Change to Mediboard directory
      $MB_PATH = dirname($currentDir);
      chdir($MB_PATH);
      
      // Change group to allow Apache to access files as group
      $chgrp = recurse_chgrp($BASE_PATH, $APACHE_GROUP);
      check_errs(
        $chgrp,
        false,
        "Failed to change files group to ".$APACHE_GROUP,
        "Files group changed to ".$APACHE_GROUP."!"
      );
      
      // Remove write access to all files for group and other
      $chmod = chmod_R($BASE_PATH, 0755, 0755);
      check_errs($chmod, false, "Failed to protect all files from writing", "Files protected from writing!");
      
      if ($BASE_PATH == dirname($currentDir)) {
        // Give write access to Apache for some directories
        foreach ($SUB_PATH as $ONE_PATH) {
          foreach (glob($ONE_PATH) as $myPATH) {
            $chmod = chmod_R($myPATH, 0765, 0765);
          }
        }
        
        check_errs(
          $chmod,
          false,
          "Failed to allow Apache writing to mandatory files",
          "Apache writing allowed for mandatory files!"
        );
      }
      
      fclose($fic);
      return 1;
    }
  }
  
  echo "Error: group ".$APACHE_GROUP." does not exist\n";
  return 0;
}

/**
 * Change group recursively
 * Based from http://www.php.net/manual/en/function.chown.php
 * 
 * @param string $mypath Path where apply changes
 * @param string $gid    GID to set
 * 
 * @return bool
 */
function recurse_chgrp($mypath, $gid) {
  $d = opendir($mypath);
  $bool = true;
  
  while (($file = readdir($d)) !== false) {
    if ($file != "." && $file != "..") {
      $typepath = $mypath."/".$file;
      
      if (filetype($typepath) == 'dir') {
        recurse_chgrp($typepath, $gid);
      }
      
      if (!(chgrp($typepath, $gid))) {
        $bool = false;
      }
    }
  }
  
  return $bool;
}

/**
 * Recursive chmod
 * From http://fr.php.net/manual/en/function.chmod.php
 * 
 * @param string $path     Path where to change mod
 * @param string $filemode Filemod
 * @param string $dirmode  Dirmod
 * 
 * @return bool
 */
function chmod_R($path, $filemode, $dirmode) {
  if (is_dir($path)) {
    if (!chmod($path, $dirmode)) {
      $dirmode_str = decoct($dirmode);
      echo "Failed applying filemode '$dirmode_str' on directory '$path'\n";
      echo "  `-> the directory '$path' will be skipped from recursive chmod\n";
      
      return false;
    }
    
    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
      // skip self and parent pointing directories
      if ($file != '.' && $file != '..') {
        $fullpath = $path.'/'.$file;
        chmod_R($fullpath, $filemode, $dirmode);
      }
    }
    
    closedir($dh);
  }
  else {
    if (is_link($path)) {
      echo "link '$path' is skipped\n";
      
      return;
    }
    
    if (!chmod($path, $filemode)) {
      $filemode_str = decoct($filemode);
      echo "Failed applying filemode '$filemode_str' on file '$path'\n";
      
      return false;
    }
  }
  
  return true;
}

/**
 * The Procedure for the setup function
 * 
 * @param object $backMenu The Menu for return
 * 
 * @return None
 */
function setupProcedure($backMenu) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  echo "Select an optional sub directory [default none]:\n\n";
  echo "[1] modules\n";
  echo "[2] style\n";
  echo "[3] No sub directory\n";
  
  $qt_subDir  = $procedure->createQuestion("\nSelected sub directory: ");
  $subDir     = $procedure->askQuestion($qt_subDir);
  
  switch ($subDir) {
    case "1":
      $subDir = "modules";
      break;
      
    case "2":
      $subDir = "style";
      break;
      
    case $choice:
      $procedure->clearScreen();
      $procedure->showMenu($backMenu, true);
      exit();
  }
  
  $qt_apacheGrp    = $procedure->createQuestion("\nApache user's group [optional]: ");
  $apacheGrp       = $procedure->askQuestion($qt_apacheGrp);
  
  echo "\n";
  setup($subDir, $apacheGrp);
}

/**
 * Function to use setup in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function setupCall( $command, $argv ) {
  if (count($argv) == 2) {
    $subDir     = $argv[0];
    $apacheGrp  = $argv[1];
    
    setup($subDir, $apacheGrp);
    return 0;
  }
  else {
    echo "\nUsage : $command setup [<sub directory>] [<apache group>]\n
Options :
[<sub directory>]     : modules|style
[<apache group>]      : name of the primary group for apache user\n\n";
    return 1;
  }
}
?>
