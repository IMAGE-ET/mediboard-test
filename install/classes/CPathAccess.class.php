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
 * File access check helper
 * Responsibilities:
 *  - path and description of path
 *  - checking
 */
class CPathAccess extends CPrerequisite {
  public $path = "";
  public $description = "";

  /**
   * Actually check path is writable
   *
   * @param bool $strict Check also warnings
   * 
   * @return bool
   */
  function check($strict = true) {
    global $mbpath;
    return is_writable($mbpath.$this->path);
  }

  /**
   * @see parent::getAll()
   */
  function getAll() {
    $pathAccesses = array();
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "tmp/";
    $pathAccess->description = "Répertoire des fichiers temporaires";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "files/";
    $pathAccess->description = "Répertoire de tous les fichiers attachés";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "lib/";
    $pathAccess->description = "Répertoire d'installation des bibliothèques tierces";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "includes/";
    $pathAccess->description = "Répertoire du fichier de configuration du système";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "modules/hprimxml/xsd";
    $pathAccess->description = "Répertoire des schemas HPRIM";
    
    $pathAccesses[] = $pathAccess;
    
    return $pathAccesses;
  }
}
