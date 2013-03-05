<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CBlocage extends CMbObject {
  public $blocage_id;

  // DB References
  public $salle_id;

  // DB Fields
  public $libelle;
  public $deb;
  public $fin;

  /** @var CSalle */
  public $_ref_salle;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'blocage';
    $spec->key   = 'blocage_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["salle_id"] = "ref class|CSalle notNull";
    $props["libelle"]  = "str seekable";
    $props["deb"]      = "date notNull";
    $props["fin"]      = "date notNull moreEquals|deb";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "Blocage du " . $this->getFormattedValue("deb") . " au " . $this->getFormattedValue("fin");
  }

  /**
   * @return CSalle
   */
  function loadRefSalle() {
    return $this->_ref_salle = $this->loadFwdRef("salle_id", true);
  }
}
