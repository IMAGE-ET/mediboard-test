<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Romain Ollivier
*/

require_once("bcbObject.class.php");

class CBcbControleProfil extends CBcbObject {
  
  var $_ref_patient = null;
  
  // Constructeur
  function CBcbControleProfil(){
    $this->distClass = "BCBControleProfil";
    parent::__construct();
  }
  
  function addProduit($code_cip) {
    $this->distObj->AddCIP($code_cip);
  }
  
  function addCIM($code_cim) {
    $this->distObj->AddPathologie($code_cim, 1);
  }
  
  function setPatient($patient) {
    if($patient->_class_name == "CPatient") {
      $this->_ref_patient = $patient;
      return true;
    }
    return false;
  }
  
  function testProfil() {
    if($this->_ref_patient) {
      if($this->_ref_patient->sexe == "m") {
        $sexe = "M";
      } else {
        $sexe = "F";
      }
      if($this->_ref_patient->_age <= 15) {
        $age = $this->_ref_patient->_age;
      } else {
        $age = 0;
      }
      return $this->distObj->Test($age, 0, 0, 0, $sexe);
    } else {
      return false;
    }
  }
  
}
