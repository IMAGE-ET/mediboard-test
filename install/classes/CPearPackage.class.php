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
 * PEAR package prerequisite
 */
class CPearPackage extends CPrerequisite {
  var $status = "stable";

  /**
   * Check file inclusion
   *
   * @param bool $strict Check also warnings
   * 
   * @see parent::check
   * 
   * @return bool
   */
  function check($strict = true) {
    if (!$this->mandatory && !$strict) {
      return true;
    }

    return @include_once "$this->name.php";
  }

  /**
   * @return self[]
   */
  function getAll(){
    $packages = array();
    
    $package = new CPearPackage;
    $package->name = "Archive/Tar";
    $package->description = "Package de manipulation d'archives au format GNU TAR";
    $package->mandatory = true;
    $package->reasons[] = "Installation de Mediboard";
    $package->reasons[] = "Import des fonctions de GHM";
    $packages[] = $package;
    
    $package = new CPearPackage;
    $package->name = "Config";
    $package->description = "Package de manipulation de fichiers de configuration";
    $package->mandatory = true;
    $package->reasons[] = "Configuration générale de Mediboard";
    $packages[] = $package;
    
    $package = new CPearPackage;
    $package->name = "DB";
    $package->description = "Package de manipulation de base de données";
    $package->mandatory = true;
    $package->reasons[] = "Assistant d'installation de Mediboard";
    $packages[] = $package;
    
    $package = new CPearPackage;
    $package->name = "PHP/CodeSniffer";
    $package->description = "Analyseur syntaxique de code source";
    $package->status = "beta";
    $package->mandatory = false;
    $package->reasons[] = "Outil de génie logiciel pour vérifier la qualité du code source de Mediboard";
    $packages[] = $package;
    
    return $packages;
  }
}

