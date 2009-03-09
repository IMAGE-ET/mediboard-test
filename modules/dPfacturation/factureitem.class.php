<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Alexis / Yohann	
 */

/**
 * The CFactureItem class
 */
class CFactureItem extends CMbObject {
  // DB Table key
  var $factureitem_id = null;
  
  // DB Fields
  var $facture_id = null;
  var $libelle = null;
  var $prix_ht = null;
  var $taxe = null;
  
  // References
  var $_ref_facture = null;
   
  var $_ttc = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'factureitem';
    $spec->key   = 'factureitem_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["facture_id"] = "ref notNull class|CFacture";
    $specs["libelle"]    = "text notNull";
    $specs["prix_ht"]    = "currency notNull";
    $specs["taxe"]       = "pct notNull";
    $specs["_ttc"]		   = "currency";
    return $specs;
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    $this->_ttc += $this->prix_ht * ($this->taxe/100) + $this->prix_ht;
  }
  
  function loadRefsFwd(){ 
  	$this->_ref_facture = new CFacture;
  	$this->_ref_facture->load($this->facture_id);
  }
}
?>