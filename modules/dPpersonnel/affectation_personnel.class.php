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
  var $user_id = null;
  
  // DB fields
  var $realise = null;
  var $debut   = null;
  var $fin     = null;

  // Form fields
  var $_debut  = null;
  var $_fin    = null;
  
  // References
  var $_ref_user = null;
  
  function CAffectationPersonnel() {
	$this->CMbObject("affectation_personnel", "affect_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["user_id"] = "notNull ref class|CMediusers";
    $specs["realise"] = "notNull bool";
    $specs["debut"]   = "dateTime";
    $specs["fin"]     = "dateTime moreThan|debut";
    return $specs;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadUser();
  }
  
  function loadUser(){
  	$this->_ref_user = new CMediusers();
  	$this->_ref_user->load($this->user_id);
  }
 
  function updateFormFields() {
    $this->_view = "Affectation de $this->user_id";
    $this->loadRefs();  
    if($this->object_class == "CPlageOp"){
    	$this->_debut = mbAddDateTime($this->_ref_object->debut, $this->_ref_object->date);
    	$this->_fin = mbAddDateTime($this->_ref_object->fin, $this->_ref_object->date);
    }
  }
  
}
?>