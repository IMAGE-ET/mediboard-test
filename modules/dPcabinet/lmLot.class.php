<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

CAppUI::requireModuleClass("dPcabinet", "lmObject");

/**
 * Lot de FSE produites par LogicMax
 */
class CLmLot extends CLmObject {  
  // DB Table key
  var $S_LOT_NUMERO = null;
  
  var $_annule = null;

  // DB Fields : see getProps();

  // Filter Fields
  var $_date_min = null;
  var $_date_max = null;
  
  // References
  var $_ref_id = null;
  
  // Distant field
  var $_consult_id = null;

	function updateFormFields() {
	  parent::updateFormFields();
	  $this->_annule = $this->S_LOT_ETAT == "?";
	}
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CConsultation';
    $spec->table   = 'S_F_LOT';
    $spec->key     = 'S_LOT_NUMERO';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    
    // DB Fields
    $specs["S_LOT_NUMERO"]            = "ref class|CLmLot";
    $specs["S_LOT_ETAT"]              = "enum list|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16";
    $specs["S_LOT_CPS"]               = "num";
    $specs["S_LOT_DATE"]              = "date";
    $specs["S_LOT_FIC"]               = "ref class|CLmFichier";
    $specs["S_LOT_NB_FSE"]            = "num";
    $specs["S_LOT_MODE_SECURISATION"] = "num";
    $specs["S_LOT_NB_TRANS"]          = "num";

    // Filter Fields
    $specs["_date_min"] = "date";
    $specs["_date_max"] = "date moreThan|_date_min";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fses"] = "CLmFSE S_FSE_NUM_LOT";
    return $backProps;
  }
}

?>