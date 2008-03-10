<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescription class
 */
class CPrescriptionLineComment extends CMbObject {
  // DB Table key
  var $prescription_line_comment_id = null;
  
  // DB Fields
  var $prescription_id         = null;
  var $commentaire             = null;
  var $chapitre                = null;
  
  var $_ref_element_prescription = null;
    
  function CPrescriptionLineComment() {
    $this->CMbObject("prescription_line_comment", "prescription_line_comment_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id" => "notNull ref class|CPrescription",
      "chapitre"        => "notNull enum list|medicament|dmi|anapath|biologie|imagerie|consult|kine|soin",
      "commentaire"     => "text"
    );
    return array_merge($specsParent, $specs);
 
  }
  
  function loadRefElement(){
  	$this->_ref_element_prescription = new CElementPrescription();
  	$this->_ref_element_prescription->load($this->element_prescription_id);	
  }
}

?>