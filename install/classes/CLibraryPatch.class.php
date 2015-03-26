<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

/**
 * Library patch. Modification of the original library
 */
class CLibraryPatch {
  public $dirName    = "";
  public $subDirName = "";
  public $sourceName = "";
  public $targetDir  = "";

  function getRootPath() {
    return __DIR__ . "/../../";
  }

  /**
   * Apply the patch to the library
   *
   * @return bool
   */
  function apply() {
    $mbpath = $this->getRootPath();

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
