<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
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
	var $medecin_id = null;
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
    
  function getProps() {
    $specs = parent::getProps();
    $specs["medecin_id"] = "ref notNull class|CMedecin";
    $specs["patient_id"] = "ref notNull class|CPatient";
    return $specs;
  }
    
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefMedecin();
  }
  
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
  }
  
  function loadRefMedecin() {
    return $this->_ref_medecin = $this->loadFwdRef("medecin_id");
  }
}
?>