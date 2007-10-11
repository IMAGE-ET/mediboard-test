<?php /* $Id: salle.class.php 2229 2007-07-10 16:12:37Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CAffectationPersonnel class
 */
class CAffectationPersonnel extends CMbMetaObject {
  // DB Table key
  var $affect_id = null;
  
  // DB references
  var $personnel_id = null;
  
  // DB fields
  var $realise = null;
  var $debut   = null;
  var $fin     = null;

  // Form fields
  var $_debut  = null;
  var $_fin    = null;
  
  // References
  var $_ref_personnel = null;
  var $_ref_object = null;
  
  function CAffectationPersonnel() {
	$this->CMbObject("affectation_personnel", "affect_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["personnel_id"] = "notNull ref class|CPersonnel";
    $specs["realise"] = "notNull bool";
    $specs["debut"]   = "dateTime";
    $specs["fin"]     = "dateTime moreThan|debut";
    return $specs;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadPersonnel();
  }
  
  function loadPersonnel(){
  	$this->_ref_personnel = new CPersonnel();
  	$this->_ref_personnel->load($this->personnel_id);
  }
  
  function loadRefObject(){
   
      $this->_ref_object = new $this->object_class;
      $this->_ref_object->load($this->object_id);
    
  }
 
  function updateFormFields() {
    $this->_view = "Affectation de $this->personnel_id";
    $this->loadRefs();  
    if($this->object_class == "CPlageOp"){
    	$this->_debut = mbAddDateTime($this->_ref_object->debut, $this->_ref_object->date);
    	$this->_fin = mbAddDateTime($this->_ref_object->fin, $this->_ref_object->date);
    }
  }
  
}
?>