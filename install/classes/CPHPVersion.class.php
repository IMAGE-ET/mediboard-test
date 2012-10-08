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

/**
 * PHP version prerequisite
 */
class CPHPVersion extends CPrerequisite {

  /**
   * Compare PHP version
   * 
   * @see parent::check
   * 
   * @return bool
   */
  function check($strict = true) {
    return phpversion() >= $this->name;
  }
  
  function getAll() {
    $versions = array();
    
    // Do not use $version which is a Mediboard global
    $php = new CPHPVersion;
    $php->name = "5.2";
    $php->mandatory = true;
    $php->description = "Version de PHP5 récente";
    $php->reasons[] = "Intégration du support XML natif : utilisation pour l'intéropérabilité HPRIM XML'";
    $php->reasons[] = "Intégration de PDO : accès universel et sécurisé aux base de données";
    $php->reasons[] = "Conception objet plus évoluée";
    $versions[] = $php;
    
    return $versions;
  }
}
