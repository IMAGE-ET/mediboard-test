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
  public $consult_id;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CConsultation */
  public $_ref_consult;

  /** @var CPrescriptionLineElement */
  public $_ref_prescription_line_element;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_task';
    $spec->key   = 'sejour_task_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["description"] = "text notNull helped";
    $props["realise"]     = "bool default|0";
    $props["resultat"]    = "text helped";
    $props["prescription_line_element_id"] = "ref class|CPrescriptionLineElement";
    $props["consult_id"]  = "ref class|CConsultation";

    return $props;
  }

  /**
   * @see  parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->description;
  }

  /**
   * Charge le séjour relié à la tâche
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Charge la consultation reliée à la tâche
   *
   * @return CConsultation
   */
  function loadRefConsult() {
    return $this->_ref_consult = $this->loadFwdRef("consult_id", true);
  }

  /**
   * Charge la ligne d'élément reliée à la tâche
   *
   * @return CPrescriptionLineElement
   */
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
