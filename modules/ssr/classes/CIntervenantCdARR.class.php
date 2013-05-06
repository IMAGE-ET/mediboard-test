<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Intervenant d'activité CdARR
 */
class CIntervenantCdARR extends CCdARRObject {  
  public $code;
  public $libelle;

  static $cached = array();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'intervenant';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|2";
    $props["libelle"] = "str notNull maxLength|50";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code - $this->libelle";
    $this->_shortview = $this->code;
  }

  /**
   * Get an instance from the code
   *
   * @param string $code
   *
   * @return CIntervenantCdARR
   **/
  static function get($code) {
    if (!isset(self::$cached[$code])) {
      $intervenant = new CIntervenantCdARR();
      $intervenant->load($code);
      self::$cached[$code] = $intervenant;
    }
    return self::$cached[$code];
  }
}
