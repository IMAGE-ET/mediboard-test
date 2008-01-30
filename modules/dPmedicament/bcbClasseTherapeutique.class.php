<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbClasseTherapeutique extends CBcbObject {
  // Générale
  var $distObj               = null;
 
  // Constructeur
  function CBcbClasseTherapeutique(){
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new BCBClasseTherapeutique();
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase);
  }
 
  // Fonction qui retourne les classes Therapeutiques du produit
  function searchTheraProduit($CIP){
    // Chargement des classes Therapeutiques du produit
    $this->distObj->ClasseProduit($CIP);
    // Parcours des classes Therapeutiques du produit
    foreach($this->distObj->tabClasseTherapeutique as $key => $classeThera){
      $length = strlen($classeThera->Code);
      for($i=1; $i<= $length; $i++) {
        $classeThera->classes[$i]["code"] = substr($classeThera->Code, 0, $i);
        $classeThera->classes[$i]["libelle"] = $this->getLibelle($classeThera->classes[$i]["code"]);
      }
    }
   
    return $this->distObj->tabClasseTherapeutique;
  }
  
  
  // Fonction permettant d'obtenir le libelle d'une classe Therapeutique
  function getLibelle($codeThera){
    $classeThera = new CBcbClasseTherapeutique();
    return $classeThera->distObj->Libelle($codeThera);
  }
  
}
