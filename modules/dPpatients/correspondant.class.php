<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: $
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Liaison entre le médecin et le patient
 */
class CCorrespondant extends CMbObject {
  
  // DB Table key
	var $correspondant_id = null;

  // DB Fields
	var $medicin_id = null;
  var $patient_id = null;

  // Object References
  var $_ref_medecin = null;
  var $_ref_patient = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'correspondant';
    $spec->key   = 'correspondant_id';
    return $spec;
  }
    
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["medicin_id"] = "notNull ref class|CMedecin";
    $specs["patient_id"] = "notNull ref class|CPatient";
    return $specs;
  }
  
  function countPatients() {
    $this->_count_patients_traites = $this->countBackRefs("patients_traites");
    $this->_count_patients1 = $this->countBackRefs("patients1");
    $this->_count_patients2 = $this->countBackRefs("patients2");
    $this->_count_patients3 = $this->countBackRefs("patients3");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  }
	 
  function loadRefsFwd() {
    $patient = new CPatient();
    $this->_ref_patient = $patient->getCached($this->patient_id);
    
    $medecin = new CMedecin();
    $this->_ref_medecin = $patient->getCached($this->medecin_id);
  }
}
?>