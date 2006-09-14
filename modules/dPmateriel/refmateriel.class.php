<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CRefMateriel class
 */
class CRefMateriel extends CMbObject {
  // DB Table key
  var $reference_id = null;
  
  // DB Fields
  var $materiel_id = null;
  var $fournisseur_id = null;
  var $quantite = null;
  var $prix = null;
  
  // Object References
  var $_ref_fournisseur = null;
  var $_ref_materiel = null;
  
  //
  var $_prix_unitaire = null;
  
  function CRefMateriel() {
    $this->CMbObject("ref_materiel", "reference_id");
    
    $this->_props["fournisseur_id"] = "ref|notNull";
    $this->_props["materiel_id"] = "ref|notNull";
    $this->_props["quantite"] = "num|pos|notNull";
    $this->_props["prix"] = "num|pos|notNull";
  }	  	
  
  function updateFormFields() {
    parent::updateFormFields();
    if($this->quantite!=0){
      $this->_prix_unitaire = $this->prix / $this->quantite;
    }
  }
  
  function loadRefsFwd(){  
    $this->_ref_fournisseur = new CFournisseur;
    $this->_ref_fournisseur->load($this->fournisseur_id);
    
    $this->_ref_materiel = new CMateriel;
    $this->_ref_materiel->load($this->materiel_id);	
  }
}
?>