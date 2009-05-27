<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// PEAR Throws 
require_once ("Archive/Tar.php");
require_once ("Archive/Zip.php");

class CMbPath {
  /**
   * Ensures a directory exists by building all tree sub-diriectories if possible
   * @param string $dir directory path
   * @param octal $chmod like value
   * @return boolean job done
   */
  static function forceDir($dir, $mode = 0755) {
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
  
  /**
   * @returns true if directory is empty
   */
  static function isEmptyDir($dir) {
    if (false === $dh = opendir($dir)) {
      trigger_error("Passed argument is not a valid directory or couldn't be opened'", E_USER_WARNING);
      return false;
    }
    
    $file = readdir($dh); // for ./
    $file = readdir($dh); // for ../
    $file = readdir($dh); // for real first child

    closedir($dh);

    return $file === null;             
  }
  
  /**
   * Removes all empty sub-directories of a given directory
   * @return integer removed directories count
   */
  static function purgeEmptySubdirs($dir) {
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
  
  static function getExtension($path) {
    $info = pathinfo($path);
    return $info['extension'];
  }
  
  /**
   * Extracts an archive into a destination directory
   * @return the number of extracted files or false if failed
   */
  static function extract($archivePath, $destinationDir) {
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
    }
    
    if (!$extract) {
      return false;
    }
    
    return $nbFiles;
  }
  
	/**
	 * Clears out any file or sub-directory from target path
	 * @return boolean jobdone-value */
	static function emptyDir($dir) {
	  if (!($dir = dir($dir))) {
	    return false;
	  }
	  
	  while (false !== $item = $dir->read()) {
	    if ($item != '.' && $item != '..' && !CMbPath::remove($dir->path . DIRECTORY_SEPARATOR . $item)) {
	      $dir->close();
	      return false;
	    }
	  }
	  
	  $dir->close();
	  return true;
	}
	
	/**
	 * Recursively removes target path
	 * @return boolean jobdone-value */
	static function remove($path) {
	  if (!$path) {
	    trigger_error("Path undefined", E_USER_WARNING);
	  }
	  
	  if (is_dir ($path)) {
	    if (CMbPath::emptyDir($path)) {
	      return rmdir ($path);
	    }
	    return false;
	  }
	  
	  return unlink($path);
	}
  
  
  /**
   * Reduces a path, removing "folder/.." occurences
   * @param $path The path to reduces
   * @return The reduced path
   */ 
  static function reduce($path) {
    while(preg_match('/([A-z0-9-_])+\/\.\.\//', $path)) {
      $path = preg_replace('/([A-z0-9-_])+\/\.\.\//', '', $path);
    }
    return $path;
  }
}

?>