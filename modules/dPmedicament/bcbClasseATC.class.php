<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbClasseATC extends CBcbObject {
  // Générale
  var $distObj               = null;
 
  // Constructeur
  function CBcbClasseATC(){
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new BCBClasseATC();
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase);
  }
 
  // Fonction qui retourne les classes ATC du produit
  function searchATCProduit($CIP){
    // Chargement des classes ATC du produit
    $this->distObj->ClasseProduit($CIP);
    // Parcours des classes ATC du produit
    foreach($this->distObj->tabClasseATC as $key => $classeATC){
      $length = strlen($classeATC->Code);
      for($i=1; $i<= $length; $i++) {
        $classeATC->classes[$i]["code"] = substr($classeATC->Code, 0, $i);
        $classeATC->classes[$i]["libelle"] = $this->getLibelle($classeATC->classes[$i]["code"]);
      }
    }
    return $this->distObj->tabClasseATC;
  }
  
  
  // Fonction permettant d'obtenir le libelle d'une classe ATC
  function getLibelle($codeATC){
    $classeATC = new CBcbClasseATC();
    return $classeATC->distObj->Libelle($codeATC);
  }
  
}
