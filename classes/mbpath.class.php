<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */

require_once ("Archive/Tar.php");
require_once ("Archive/Zip.php");

class CMbPath {
  /**
   * Ensures a directory exists by building all tree sub-diriectories if possible
   * @param string $dir directory path
   * @return boolean job done
   */
  function forceDir($dir, $mode = 0755) {
    if (!$dir) {
      trigger_error("Directory is null", E_USER_WARNING);
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
  
  function remove($dir) {
    return mbRemovePath($dir);
  }
  
  /**
   * @returns true if directory is empty
   */
  function isEmptyDir($dir) {
    if (false === $dh = opendir($dir)) {
      trigger_error("Passed argument is not a valid directory or couldn't be opened'", E_USER_WARNING);
      return false;
    }
    
    $file = readdir($dh); // for ./
    $file = readdir($dh); // for ../
    $file = readdir($dh); // for real first child

    closedir($dh);

    return $file == null;             
  }
  
  /**
   * Removes all empty sub-directories of a given directory
   * @return integer removed directories count
   */
  function purgeEmptySubdirs($dir) {
    $removedDirsCount = 0;
    
    if (false === $dh = opendir($dir)) {
      trigger_error("Passed argument is not a valid directory or couldn't be opened'", E_USER_WARNING);
      return 0;
    }
    
    while ($node = readdir($dh)) {
      $path = "$dir/$node";
      if (is_dir($path) and $node != "." and $node != "..") {
        $removedDirsCount += CMbPath::purgeEmptySubdirs($path);
      }
    }
    closedir($dh);
    
    if (CMbPath::isEmptyDir($dir)) {
      if (rmdir($dir)) {
        mbTrace($dir, "Removed directory");
        $removedDirsCount++;
      }
    }
    
    return $removedDirsCount;
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