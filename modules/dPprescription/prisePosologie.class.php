<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CMomentUnitaire class
 */
class CPrisePosologie extends CMbObject {
  
	// DB Table key
  var $prise_posologie_id = null;
  
  // DB Fields
  var $prescription_line_id  = null;
  var $moment_unitaire_id    = null;
  var $quantite              = null;
  
  function CPrisePosologie() {
    $this->CMbObject("prise_posologie", "prise_posologie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_line_id" => "ref class|CPrescriptionLineMedicament notNull cascade",
      "moment_unitaire_id"   => "ref class|CMomentUnitaire notNull",
      "quantite"             => "num max|1000"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
  }
}
  
?>