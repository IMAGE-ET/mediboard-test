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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ref_materiel';
    $spec->key   = 'ref_materiel_id';
    return $spec;
  }
 
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["commandes_materiel"] = "CCommandeMateriel reference_id";
     return $backRefs;
 }
 
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "fournisseur_id" => "ref notNull class|CFournisseur",
      "materiel_id"    => "ref notNull class|CMateriel",
      "quantite"       => "num notNull pos",
      "prix"           => "float notNull"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_materiel->_view;
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