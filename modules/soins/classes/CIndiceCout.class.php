<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CIndiceCout extends CMbObject {
  public $indice_cout_id;
  
  // DB Fields
  public $nb;
  public $ressource_soin_id;
  public $element_prescription_id;
  
  /** @var CRessourceSoin */
  public $_ref_ressource_soin;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'indice_cout';
    $spec->key   = 'indice_cout_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["nb"]                      = "num notNull";
    $props["ressource_soin_id"]       = "ref class|CRessourceSoin notNull";
    $props["element_prescription_id"] = "ref class|CElementPrescription notNull";
    return $props;
  }

  /**
   * @return CRessourceSoin
   */
  function loadRefRessourceSoin() {
    return $this->_ref_ressource_soin = $this->loadFwdRef("ressource_soin_id", true);
  }
}
