<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

class CLibraryPatch {
  var $dirName    = "";
  var $subDirName = "";
  var $sourceName = "";
  var $targetDir  = "";
  
  function apply() {
    global $mbpath;
    $pkgsDir = $mbpath."libpkg";
    $libsDir = $mbpath."lib";
    $patchDir = "$pkgsDir/patches";
    $sourcePath = "$patchDir/$this->dirName/";
    if ($this->subDirName) {
      $sourcePath .= "$this->subDirName/";
    }
    $sourcePath .= "$this->sourceName";
    $targetPath = "$libsDir/$this->dirName/$this->targetDir/$this->sourceName";
    $oldPath = $targetPath . ".old";
    
    @unlink($oldPath);
    @rename($targetPath, $oldPath);
    return copy($sourcePath, $targetPath);
  }
}
