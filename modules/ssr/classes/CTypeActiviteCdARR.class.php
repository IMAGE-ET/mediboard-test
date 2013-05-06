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
 * Catégorie d'activité CdARR
 */
class CTypeActiviteCdARR extends CCdARRObject {
  public $code;
  public $libelle;
  public $libelle_court;

  static $cached = array();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'type_activite';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]          = "str notNull length|4";
    $props["libelle"]       = "str notNull maxLength|50";
    $props["libelle_court"] = "str notNull maxLength|50";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view      = "($this->code) $this->libelle";
    $this->_shortview = "($this->code) $this->libelle_court";
  }

  /**
   * Get an instance from the code
   * @param $code string
   * @return CTypeActiviteCdARR
   **/
  static function get($code) {
    if (!isset(self::$cached[$code])) {
      $type = new CTypeActiviteCdARR();
      if ($type->load($code)) {
        self::$cached[$code] = $type;
      }
    }
    return self::$cached[$code];
  }
}
