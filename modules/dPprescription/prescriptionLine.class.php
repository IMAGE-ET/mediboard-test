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
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id" => "notNull ref class|CPrescription cascade",
      "ald"             => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefPrescription(){
  	$this->_ref_prescription = new CCPrescription();
  	$this->_ref_prescription->load($prescription_id);
  }
  
  

}

?>