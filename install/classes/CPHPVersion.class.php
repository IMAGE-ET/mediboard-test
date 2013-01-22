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
   * @param bool $strict Check also warnings
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
    $php->name = "5.3";
    $php->mandatory = true;
    $php->description = "Version de PHP5 récente";
    $php->reasons[] = "Conception objet évoluée";
    $php->reasons[] = "Optimisation mémoire";
    $php->reasons[] = "Performances";
    $versions[] = $php;

    return $versions;
  }
}
