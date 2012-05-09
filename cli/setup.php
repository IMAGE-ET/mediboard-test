<?php 
require_once ("utils.php");

function setup($mediboardDir, $subDir, $apacheGrp) {

  $currentDir = dirname(__FILE__);
  
  announce_script("Mediboard directories groups and mods");
  
  $darwin_kernel = PHP_OS;
  
  // To MAC
  if ($darwin_kernel == "Darwin") {
  
    $APACHE_USER = shell_exec("ps -ef|grep httpd|head -2|tail -1|cut -d' ' -f4");
    $APACHE_GROUP = shell_exec("groups ".$APACHE_USER." | cut -d' ' -f1");
  }
  // To Linux distributions
  else {
  
    $APACHE_USER = shell_exec("ps -ef|grep apache|head -2|tail -1|cut -d' ' -f1");
    $APACHE_GROUP = shell_exec("groups ".$APACHE_USER." | cut -d' ' -f3");
  }
  
  if ($apacheGrp != NULL) {
  
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
          $SUB_PATH = array("lib/", "tmp/", "files/", "includes/", "modules/*/locales/", "modules/hprimxml/xsd/", "locales/*/");
      }
      
      // Change to Mediboard directory
      $MB_PATH = dirname($currentDir);
      
      chdir($MB_PATH);
      
      // Change group to allow Apache to access files as group
      $chgrp = recurse_chgrp($BASE_PATH, $APACHE_GROUP);
      check_errs($chgrp, false, "Failed to change files group to ".$APACHE_GROUP, "Files group changed to ".$APACHE_GROUP."!");
      
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
        
        check_errs($chmod, false, "Failed to allow Apache writing to mandatory files", "Apache writing allowed for mandatory files!");
      }
      
      fclose($fic);
      
      return 1;
    }
  }
  
  echo "Error: group ".$APACHE_GROUP." does not exist\n";
  return 0;

  
}

// Based from http://www.php.net/manual/en/function.chown.php
function recurse_chgrp($mypath, $gid) {
  $d = opendir($mypath);
  $bool = true;
  
  while (($file = readdir($d)) !== false) {
    if ($file != "." && $file != "..") {
    
      $typepath = $mypath."/".$file;
      
      //print $typepath. " : " . filetype ($typepath). "<BR>" ;
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

// From http://fr.php.net/manual/en/function.chmod.php
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
    
      if ($file != '.' && $file != '..') { // skip self and parent pointing directories
      
        $fullpath = $path.'/'.$file;
        chmod_R($fullpath, $filemode, $dirmode);
      }
    }
    
    closedir($dh);
  } else {
  
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
?>
