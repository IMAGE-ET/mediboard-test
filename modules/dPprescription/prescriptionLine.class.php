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
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id" => "notNull ref class|CPrescription cascade",
      "ald"             => "bool",
      "praticien_id"    => "ref class|CMediusers"
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

}

?>