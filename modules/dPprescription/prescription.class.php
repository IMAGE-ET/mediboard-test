<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescription extends CMbObject {
  // DB Table key
  var $prescription_id = null;
  
  // DB Fields
  var $praticien_id = null;
  var $object_class = null;
  var $object_id    = null;
  
  // Object References
  var $_ref_object = null;
  
  // BackRefs
  var $_ref_prescription_lines = null;
  
  function CPrescription() {
    $this->CMbObject("prescription", "prescription_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_line"] = "CPrescriptionLine prescription_id";
    return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "praticien_id"  => "notNull ref class|CMediusers",
      "object_id"     => "ref class|CCodable meta|object_class",
      "object_class"  => "notNull enum list|CSejour|CConsultation",
    );
  }
  
  function getSeeks() {
    return array (
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "Prescription du Dr. ".$this->_ref_praticien->_view." : ".$this->_ref_object->_view;
  }
  
  function loadRefsFwd() {
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);
    $this->_ref_object = new $this->object_class();
    $this->_ref_object->load($this->object_id);
  }
  
  function loadRefsLines() {
    $line = new CPrescriptionLine();
    $where = array("prescription_id" => "= $this->_id");
    $order = "prescription_line_id";
    $this->_ref_prescription_lines = $line->loadList($where, $order);
  }
  
}

?>