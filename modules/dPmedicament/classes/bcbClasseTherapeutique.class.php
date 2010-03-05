<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbClasseTherapeutique extends CBcbObject {
 
  // Constructeur
  function CBcbClasseTherapeutique(){
    $this->distClass = "BCBClasseTherapeutique";
    parent::__construct();
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
  
  

  
  // Fonction qui retourne l'arborescence
  function loadArbre($classeBCB = ""){
    $this->distObj->Arbre($classeBCB);
    return $this->distObj->tabClasseTherapeutique;
  }
  
  // Fonction qui retourne les produits d'une classe ATC en fonction du code de la classe
  function loadRefsProduits($codeBCB = ""){
    $this->distObj->Produits($codeBCB);
    $this->_refs_produits = $this->distObj->tabProduit;
  }
  
  
  // Fonction qui retourne le niveau du code dans l'arbre
  function getNiveau($codeBCB){
    return strlen($codeBCB);
  }
  
  function getCodeNiveauSup($codeBCB){
    if(strlen($codeBCB)==0){
      return;
    }
    $code = substr($codeBCB, 0, strlen($codeBCB)-1); 
    $libelle = $this->getLibelle($code);
    if($libelle == ""){
      return $this->getCodeNiveauSup($code); 
    }
    return $code;
  }
  
  // Fonction permettant d'obtenir le libelle d'une classe Therapeutique
  function getLibelle($codeThera){
    $classeThera = new CBcbClasseTherapeutique();
    return $classeThera->distObj->Libelle($codeThera);
  }
  
}

?>