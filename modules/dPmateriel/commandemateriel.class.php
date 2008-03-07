<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CCommandeMateriel class
 */
class CCommandeMateriel extends CMbObject {
  // DB Table key
  var $commande_materiel_id = null;
	
  // DB Fields
  var $reference_id = null;
  var $quantite     = null;
  var $prix         = null;
  var $date         = null;
  var $recu         = null;

  // Object References
  var $_ref_reference = null;

	function CCommandeMateriel() {
	  $this->CMbObject("commande_materiel", "commande_materiel_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "reference_id" => "notNull ref class|CRefMateriel",
      "quantite"    => "notNull num pos",
      "prix"        => "notNull currency",
      "date"        => "notNull date",
      "recu"        => "notNull bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = mbTranformTime(null, $this->date, "%d/%m/%Y")." - ".$this->_ref_reference->_view;
  }
  
  function loadRefsFwd(){
    $this->_ref_reference = new CRefMateriel();
    $this->_ref_reference->load($this->reference_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_reference) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_reference->getPerm($permType));
  }
}
?>