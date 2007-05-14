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
  
  function CFactureItem() {
    $this->CMbObject("factureitem", "factureitem_id"); 
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "facture_id"  => "notNull ref class|CFacture",
      "libelle"     => "notNull text",
      "prix_ht"     => "notNull currency",
      "taxe"        => "notNull pct"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    $this->_ttc += $this->prix_ht * $this->taxe;
  }

  function loadRefsBack(){

  } 
  
  function loadRefsFwd(){ 
	$this->_ref_facture = new CFacture;
	$this->_ref_facture->load($this->facture_id);
  }
}
?>