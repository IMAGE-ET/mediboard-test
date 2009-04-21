<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
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
    if($this->_ref_patient->_id) {
      if($this->_ref_patient->_ref_dossier_medical->_id) {
        foreach($this->_ref_patient->_ref_dossier_medical->_codes_cim as $code) {
          $this->addCIM(CCodeCIM10::addPoint($code));
        }
      }
      if($this->_ref_patient->sexe == "m") {
        $sexe = "M";
      } else {
        $sexe = "F";
      }
      $nbMois = $this->_ref_patient->evalAgeMois();
      
      $poids = 0;
      $this->_ref_patient->loadRefConstantesMedicales();
      $const_med = $this->_ref_patient->_ref_constantes_medicales;
      $poids = $const_med->poids;

      return $this->distObj->Test($nbMois, $poids, 0, 0, $sexe);
    } else {
      return false;
    }
  }
  
  function getProfil() {
    $this->testProfil();
    return $this->distObj->gTabCIProfil;
  }
  
}

?>