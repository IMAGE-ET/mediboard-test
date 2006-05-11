<?php /* $Id: mbobject.class.php 31 2006-05-05 09:55:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */

require_once ("Archive/Tar.php");
require_once ("Archive/Zip.php");

class CMbPath {
  function forceDir($dir, $mode = 0755) {
    if (!$dir) {
      return false;
    }
    
    if (is_dir($dir) || $dir === "/") {
      return true;
    }
    
    if (CMbPath::forceDir(dirname($dir))) {
      return mkdir($dir, $mode);
    }
  
    return false;
  }
  
  function getExtension($path) {
    $fragments = explode(".", basename($path));
    if (count($fragments) < 2) {
      return "";
    }
    
    return $fragments[count($fragments) - 1];
  }
  
  /**
   * Extracts an archive into a destination directory
   * @return the number of extracted files or false if failed
   */
  function extract($archivePath, $destinationDir) {
    if (!is_file($archivePath)) {
      trigger_error("Archive could not be found", E_USER_WARNING);
      return false;
    }
    
    if (!CMbPath::forceDir($destinationDir)) {
      trigger_error("Destination directory not existing", E_USER_WARNING);
      return false;
    }
    
    $nbFiles = 0;
    switch (CMbPath::getExtension($archivePath)) {
      case "gz"  :
      case "tgz" : 
      $archive = new Archive_Tar($archivePath);
      $nbFiles = count($archive->listContent());
      $extract = $archive->extract($destinationDir);
      break;
      
      case "zip" : 
      $archive = new Archive_Zip($archivePath);
      $nbFiles = count($archive->listContent());
      $extract = $archive->extract(array("add_path" => $destinationDir));
      break;
      
      default : 
      $return = false;
      break;
    }
    
    if (!$extract) {
      return false;
    }
    
    return $nbFiles;
  }
}

?>