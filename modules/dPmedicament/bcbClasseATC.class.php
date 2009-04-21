<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbClasseATC extends CBcbObject {
 
  // Object references
  var $_ref_produits = null;
  
  // Constructeur
  function CBcbClasseATC(){
    $this->distClass = "BCBClasseATC";
    parent::__construct();
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
  
 
  // Fonction qui retourne les produits d'une classe ATC en fonction du code de la classe
  function loadRefsProduits($codeATC = ""){
    $this->_ref_produits = "";
    $this->distObj->Produits($codeATC);
    $this->_ref_produits = $this->distObj->tabProduit;    
  }
  

  // Fonction qui retourne les produits du livret en fonction d'un code ATC
  function loadRefProduitsLivret($codeATC = ""){
    global $g;
    $produits = array();
    $this->distObj->ProduitClasse($codeATC, $g);
    foreach($this->distObj->tabProduit as $key => $prod){
      $produitLivret = new CBcbProduitLivretTherapeutique();
      $produitLivret->load($prod->CodeCIP);
      $produitLivret->loadRefProduit();
      $produits[] = $produitLivret;
    }
    return $produits;
  }

  
  function getCodeNiveauSup($codeATC){
    if(strlen($codeATC)==0){
      return;
    }
    $code = substr($codeATC, 0, strlen($codeATC)-1); 
    $libelle = $this->getLibelle($code);
    if($libelle == ""){
      return $this->getCodeNiveauSup($code); 
    }
    return $code;
  }
  
  
  // Fonction qui retourne le niveau du code ATC dans l'arbre
  function getNiveau($codeATC){
    switch(strlen($codeATC)) {
			case 1:
				$niveau = 1;
				break;
			case 3:
				$niveau = 2;
				break;
			case 4:
				$niveau = 3;
				break;
			case 5:
				$niveau = 4;
				break;
			case 7:
				$niveau = 5;
				break;
			default:
			  $niveau = 0;
		}
		return $niveau;
  }
  
  
  // Fonction qui retourne l'arborescence
  function loadArbre($classeATC = ""){
    $this->distObj->Arbre($classeATC);
    return $this->distObj->tabClasseATC;
  }
  
  
  
  // Fonction permettant d'obtenir le libelle d'une classe ATC
  function getLibelle($codeATC){
    $classeATC = new CBcbClasseATC();
    return $classeATC->distObj->Libelle($codeATC);
  }
  
}

?>