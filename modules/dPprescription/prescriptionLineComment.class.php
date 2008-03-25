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
class CPrescriptionLineComment extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_comment_id = null;
  
  // DB Fields
  var $commentaire               = null;
  var $category_prescription_id  = null;
  var $executant_prescription_line_id = null;
  
  var $_ref_category_prescription = null;
  var $_ref_executant = null;
  
  function CPrescriptionLineComment() {
    $this->CMbObject("prescription_line_comment", "prescription_line_comment_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "category_prescription_id"       => "ref class|CCategoryPrescription",
      "executant_prescription_line_id" => "ref class|CExecutantPrescriptionLine",
      "commentaire"                    => "text"
    );
    return array_merge($specsParent, $specs);
 
  }
  
  function loadRefCategory(){
  	$this->_ref_category_prescription = new CCategoryPrescription();
  	$this->_ref_category_prescription->load($this->category_prescription_id);	
  }
  
  function loadRefExecutant(){
    $this->_ref_executant = new CExecutantPrescriptionLine();
    $this->_ref_executant->load($this->executant_prescription_line_id);
  }
}

?>