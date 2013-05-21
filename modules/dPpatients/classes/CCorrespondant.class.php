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
 * Liaison entre le médecin et le patient
 */
class CCorrespondant extends CMbObject {
  
  // DB Table key
  public $correspondant_id;

  // DB Fields
  public $medecin_id;
  public $patient_id;

  /** @var CMedecin */
  public $_ref_medecin;
  
  /** @var CPatient */
  public $_ref_patient;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "correspondant";
    $spec->key   = "correspondant_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["medecin_id"] = "ref notNull class|CMedecin";
    $specs["patient_id"] = "ref notNull class|CPatient";
    return $specs;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefMedecin();
  }
  
  /**
   * Charge le patient
   *
   * @return CPatient
   */
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
  }
  
  /**
   * Charge le médecin
   *
   * @return CMedecin
   */
  function loadRefMedecin() {
    return $this->_ref_medecin = $this->loadFwdRef("medecin_id");
  }
}
