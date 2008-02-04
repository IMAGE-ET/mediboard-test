<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescription class
 */

class CProduitLivretTherapeutique extends CMbObject {
  
  // DB Table key
  var $produit_livret_id  = null;
  
  // DB Fields
  var $group_id           = null;
  var $libelle            = null;
  var $code_cip           = null;
  var $prix_hopital       = null;
  var $prix_ville         = null;
  var $date_prix_hopital  = null;
  var $date_prix_ville    = null;
  var $code_interne       = null;
  var $commentaire        = null;
  
  // Object reference
  var $_ref_produit = null;
  
  function CProduitLivretTherapeutique() {
    $this->CMbObject("produit_livret_therapeutique", "produit_livret_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["group_id"]          = "notNull ref class|CGroups";
    $specs["libelle"]           = "text";
    $specs["code_cip"]          = "notNull num";
    $specs["prix_hopital"]      = "currency";
    $specs["prix_ville"]        = "currency";
    $specs["date_prix_hopital"] = "date";
    $specs["date_prix_ville"] = "date";
    $specs["code_interne"]      = "num";
    $specs["commentaire"]       = "text";
    return $specs;
  }
  
  
  // Chargement du produit
  function loadRefProduit(){
    $this->_ref_produit = new CBcbProduit();
    $this->_ref_produit->load($this->code_cip);
  }
  
  
  function getSeeks() {
    return array (
    );
  }
  
}

?>