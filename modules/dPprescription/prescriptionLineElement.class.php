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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_element';
    $spec->key   = 'prescription_line_element_id';
    return $spec;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefElement();
    $this->_ref_element_prescription->loadRefCategory();
    
    $this->_view = $this->_ref_element_prescription->_view;
    
    // Un element ne peut jamais être un traitement
    $this->_traitement = 0;	
    
    $this->_unite_prise = "fois";
    
    $this->_duree_prise = "";
    
    if($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    } else {
	    if($this->debut && !$this->_fin){
	      $this->_duree_prise .= "le ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    }
	    if($this->duree && $this->_fin){
	    	$this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y")." pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree).".";
	    }
    }
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "element_prescription_id"        => "notNull ref class|CElementPrescription cascade",
      "executant_prescription_line_id" => "ref class|CExecutantPrescriptionLine",
      "commentaire"                    => "str"
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