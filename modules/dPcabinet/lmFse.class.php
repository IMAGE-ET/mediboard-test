<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Thomas Despoix
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPcabinet", "lmObject"));

/**
 * FSE produite par LogicMax
 */
class CLmFSE extends CLmObject {  
  // DB Table key
  var $FSE_NUMERO_FSE = null;

  // DB Fields : see getSpecs();
  
	function CLmFSE() {
	  $this->CLmObject("s_f_fse", "S_FSE_NUMERO_FSE");
	}
  
	function updateFormFields() {
	  parent::updateFormFields();
	  $this->_view = CAppUI::tr($this->_class_name) . " " . $this->_id;
	}
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CConsultation";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["S_FSE_MODE_SECURISATION"] = "enum list|0|1|2|3|4|5";
    $specs["S_FSE_DATE_FSE"]          = "date"            ;
    $specs["S_FSE_NUM_LOT"]           = "num"             ;
    $specs["S_FSE_TOTAL_FACTURE"]     = "currency"        ;
    $specs["S_FSE_TOTAL_AMO"]         = "currency"        ;
    $specs["S_FSE_TOTAL_ASSURE"]      = "currency"        ;
    $specs["S_FSE_TOTAL_AMC"]         = "currency"        ;
    
    return $specs;
  }
}

?>