<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Pays Insee
 */
class CPaysInsee extends CMbObject {
  // DB Fields
  public $numerique;
  public $alpha_2;
  public $alpha_3;
  public $nom_fr;
  public $nom_ISO;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = 'INSEE';
    $spec->incremented = false;
    $spec->table       = 'pays';
    $spec->key         = 'numerique';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["numerique"] = "numchar length|3";
    $specs["alpha_2"  ] = "str length|2";
    $specs["alpha_3"  ] = "str length|3";
    $specs["nom_fr"   ] = "str";
    $specs["nom_ISO"  ] = "str";
    return $specs;
  }

  /**
   * Retourne le code Alpha-3 du pays
   *
   * @param int $numerique Numero de pays
   *
   * @return string
   */
  static function getAlpha3($numerique) {
    $pays = new self;
    $pays->load($numerique);
    
    return $pays->alpha_3;
  }
}
