<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Thomas Despoix
*/

CAppUI::requireModuleClass("dPcabinet", "lmObject");

/**
 * FSE produite par LogicMax
 */
class CLmFSE extends CLmObject {  
  // DB Table key
  var $S_FSE_NUMERO_FSE = null;
  
  var $_annulee = null;

  // DB Fields : see getProps();

  // Filter Fields
  var $_date_min = null;
  var $_date_max = null;
  
  // References
  var $_ref_id = null;
  var $_ref_lot = null;
  
  // Distant field
  var $_consult_id = null;

	function updateFormFields() {
	  parent::updateFormFields();
	  $this->_annulee = $this->S_FSE_ETAT == "3";
	}
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CConsultation';
    $spec->table   = 'S_F_FSE';
    $spec->key     = 'S_FSE_NUMERO_FSE';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    
    // DB Fields
    $specs["S_FSE_NUMERO_FSE"]        = "ref class|CLmFSE";
    $specs["S_FSE_ETAT"]              = "enum list|1|2|3|4|5|6|7|8|9|10";
    $specs["S_FSE_MODE_SECURISATION"] = "enum list|0|1|2|3|4|5|255";
    $specs["S_FSE_DATE_FSE"]          = "date";
    $specs["S_FSE_CPS"]               = "num";
    $specs["S_FSE_VIT"]               = "num";
    $specs["S_FSE_NUM_LOT"]           = "ref class|CLmLot";
    $specs["S_FSE_TOTAL_FACTURE"]     = "currency";
    $specs["S_FSE_TOTAL_AMO"]         = "currency";
    $specs["S_FSE_TOTAL_ASSURE"]      = "currency";
    $specs["S_FSE_TOTAL_AMC"]         = "currency";

    // Filter Fields
    $specs["_date_min"] = "date";
    $specs["_date_max"] = "date moreThan|_date_min";
    
    // Distant field
    $specs["_consult_id"] = "ref class|CConsultation";
    
    return $specs;
  }
  
  function loadRefLot() {
    $lot = new CLmLot();
    $this->_ref_lot = $lot->getCached($this->S_FSE_NUM_LOT);
  }
  
  function loadRefIdExterne() {
    $this->_ref_id = new CIdSante400();
    $this->_ref_id->object_class = "CConsultation";
    $this->_ref_id->tag = "LogicMax FSENumero";
    $this->_ref_id->id400 = $this->_id;
    $this->_ref_id->loadMatchingObject();
    
    $this->_consult_id = $this->_ref_id->object_id;
  }
}

?>