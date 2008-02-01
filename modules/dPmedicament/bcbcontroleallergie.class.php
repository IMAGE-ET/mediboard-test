<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Romain Ollivier
*/

require_once("bcbObject.class.php");

class CBcbControleAllergie extends CBcbObject {
  
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
  
  function testAllergies() {
    return $this->distObj->Test();
  }
  
  function getAllergies() {
    $this->addAllergie(1);
    $this->addAllergie(8);
    $this->testAllergies();
    $allergies = array();
    return $this->distObj->gTabAllergie;
  }
}
