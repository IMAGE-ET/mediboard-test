<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CAssociationMoment class
 */
class CAssociationMoment extends CMbObject {
  
	// DB Table key
  var $association_moment_id = null;
  
  // DB Fields
  var $code_moment_id     = null; // Id du code_moment BCB
  var $moment_unitaire_id = null; // Id du moment unitaire
  var $OR                 = null; // Tag OR
  
  var $_ref_moment_unitaire = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'association_moment';
    $spec->key   = 'association_moment_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "code_moment_id"      => "num notNull",
      "moment_unitaire_id"  => "ref class|CMomentUnitaire notNull",
      "OR"                  => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefMomentUnitaire(){
  	$this->_ref_moment_unitaire = new CMomentUnitaire();
  	$this->_ref_moment_unitaire->load($this->moment_unitaire_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
  }
}
  
?>