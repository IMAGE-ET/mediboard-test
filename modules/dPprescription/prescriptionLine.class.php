<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescriptionLine class
 */
class CPrescriptionLine extends CMbObject {
  
  // DB Fields
  var $prescription_id = null;
  var $ald             = null;
  var $praticien_id    = null;
  var $signee           = null;
  
  // Log
  var $_ref_log_signee = null;
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id" => "notNull ref class|CPrescription cascade",
      "ald"             => "bool",
      "praticien_id"    => "ref class|CMediusers",
      "signee"          => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefPrescription(){
  	$this->_ref_prescription = new CCPrescription();
  	$this->_ref_prescription->load($this->prescription_id);
  }
  
  function loadRefPraticien(){
  	$this->_ref_praticien = new CMediusers();
  	$this->_ref_praticien->load($this->praticien_id);
  }
  
  function loadRefLogSignee(){
    $this->_ref_log_signee = $this->loadLastLogForField("signee");
  }
  

}

?>