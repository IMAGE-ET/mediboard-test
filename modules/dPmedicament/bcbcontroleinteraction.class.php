<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Romain Ollivier
*/

require_once("bcbObject.class.php");

class CBcbControleInteraction extends CBcbObject {
  
  // Constructeur
  function CBcbControleInteraction(){
    $this->distClass = "BCBControleInteraction";
    parent::__construct();
  }
  
  function addProduit($code_cip) {
    $this->distObj->AddCIP($code_cip);
  }
  
  function testInteractions() {
    return $this->distObj->Test();
  }
  
  function getInteractions() {
    $this->testInteractions();
    return $this->distObj->gtabInter;
  }
  
}
