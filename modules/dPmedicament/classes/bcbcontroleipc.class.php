<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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