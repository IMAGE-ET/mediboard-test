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
    // Chargement du niveau de gravité (erreur dans la classe BCB)
    $ds = CBcbObject::getDataSource();
    foreach($this->distObj->gtabInter as $_interaction){
      $gravite = $_interaction->Gravite;
      $query = $ds->prepare("SELECT `NIVEAUGRAVITE` FROM `INTER_GRAVITES` WHERE `LIBELLEGRAVITE` = %", $gravite);
      $_interaction->Niveau = $ds->loadResult($query);
    }
    return $this->distObj->gtabInter;
  }
}
?>