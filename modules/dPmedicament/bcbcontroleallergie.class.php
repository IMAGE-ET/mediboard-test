<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Romain Ollivier
*/

require_once("bcbObject.class.php");

class CBcbControleAllergie extends CBcbObject {
  
  static $correspCim10 = array(
    "Z880" => array(30001),                     // Penicilline
    "Z881" => array(),                          // Autres antibiotiques
    "Z882" => array(30006),                     // Sulfamide
    "Z883" => array(),                          // Autres agents anti-infectieux
    "Z884" => array(30034, 30035, 30036, 3133), // Anesthésiques
    "Z885" => array(),                          // Narcotiques
    "Z886" => array(),                          // Analgésiques
    "Z887" => array(),                          // sérum et vaccin
    "Z888" => array(),                          // Autres médicaments et substances biologiques
    "Z889" => array(),                          // Médicament et substance biologique, sans précision
  );
  
  var $_ref_patient = null;
  
  // Constructeur
  function CBcbControleAllergie(){
    $this->distClass = "BCBControleAllergie";
    parent::__construct();
  }
  
  function addAllergie($code_allergie) {
    $this->distObj->AddAllergie($code_allergie);
  }
  
  function addProduit($code_cip) {
    $this->distObj->AddCIP($code_cip);
  }
  
  function setPatient($patient) {
    if($patient->_class_name == "CPatient") {
      $this->_ref_patient = $patient;
      return true;
    }
    return false;
  }
  
  function testAllergies() {
    return $this->distObj->Test();
  }
  
  function getAllergies() {
    if($this->_ref_patient) {
      if($this->_ref_patient->_ref_dossier_medical) {
        foreach($this->_ref_patient->_ref_dossier_medical->_codes_cim as $code) {
          foreach(CBcbControleAllergie::$correspCim10 as $key_cim => $curr_corresp) {
             if($code == $key_cim) {
               foreach($curr_corresp as $curr_allergie) {
                 $this->addAllergie($curr_allergie);
               }
             }
          }
        }
      }
    }
    $this->testAllergies();
    return $this->distObj->gTabAllergie;
  }
}

?>