<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Romain Ollivier
*/

require_once("bcbObject.class.php");

class CBcbAllergie extends CBcbObject {
  
  var $code_allergie = null;
  var $libelle       = null;
  
  // Constructeur
  function CBcbAllergie(){
    $this->distClass = "BCBAllergie";
    parent::__construct();
  }
  
  function load($code_allergie) {
    $this->code_allergie = $code_allergie;
    $this->libelle = $this->distObj->Libelle($this->code_allergie);
  }
  
// Fonction qui retourne la liste des DCI qui comment par $search
  function searchAllergies($search, $limit = 50){
    $this->distObj->Search($search, 0, $limit, 1);
    return $this->distObj->Vec;
  }
  
}

?>