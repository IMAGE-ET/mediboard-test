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
class CPrescriptionLineElement extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_element_id = null;
  
  // DB Fields
  var $element_prescription_id        = null;
  var $commentaire                    = null;
  var $executant_prescription_line_id = null; 
  
  var $_ref_element_prescription = null;
  var $_ref_executant = null;
  
  function CPrescriptionLineElement() {
    $this->CMbObject("prescription_line_element", "prescription_line_element_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefElement();
    $this->_view = $this->_ref_element_prescription->_view;
    
    // Un element ne peut jamais être un traitement
    $this->_traitement = 0;	
    $this->_unite_prise = "soins";
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "element_prescription_id"        => "notNull ref class|CElementPrescription cascade",
      "executant_prescription_line_id" => "ref class|CExecutantPrescriptionLine",
      "commentaire"  => "str"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefElement(){
  	$this->_ref_element_prescription = new CElementPrescription();
  	$this->_ref_element_prescription->load($this->element_prescription_id);	
  }
  
  function loadRefExecutant(){
    $this->_ref_executant = new CExecutantPrescriptionLine();
    $this->_ref_executant->load($this->executant_prescription_line_id);
  }
}

?>