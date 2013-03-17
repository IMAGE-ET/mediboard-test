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

class CSejourTask extends CMbObject {
  public $sejour_task_id;

  // DB Fields
  public $sejour_id;
  public $description;
  public $realise;
  public $resultat;
  public $prescription_line_element_id;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CPrescriptionLineElement */
  public $_ref_prescription_line_element;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_task';
    $spec->key   = 'sejour_task_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["description"] = "text notNull helped";
    $props["realise"]     = "bool default|0";
    $props["resultat"]    = "text helped";
    $props["prescription_line_element_id"] = "ref class|CPrescriptionLineElement";
    return $props;
  }

  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->description;
  }

  /**
   * @return CSejour
   */
  function loadRefSejour() {
    $this->_ref_sejour = new CSejour();
    return $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
  }

  function loadRefPrescriptionLineElement(){
    static $active = null;

    if ($active === false) {
      return;
    }

    if ($active === true || ($active = !!CModule::getActive("dPprescription"))) {
      $this->_ref_prescription_line_element = $this->loadFwdRef("prescription_line_element_id");
    }
  }
}
