<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Romain Ollivier
*/

require_once("bcbObject.class.php");

class CBcbControleIPC extends CBcbObject {
  
  var $listCIP = array();
  
  // Constructeur
  function CBcbControleIPC(){
    $this->distClass = "BCBControleIPC";
    parent::__construct();
  }
  
  function addProduit($code_cip) {
    $this->listCIP[] = "$code_cip";
  }
  
  function testIPC() {
    return $this->distObj->Controle($this->listCIP);
  }
  
  function getIPC() {
    $this->testIPC();
    return $this->distObj->gTabIPCControle;
  }
  
}

?>