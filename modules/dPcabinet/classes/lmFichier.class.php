<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

CAppUI::requireModuleClass("dPcabinet", "lmObject");

/**
 * Fichiers de lots de FSE produites par LogicMax
 */
class CLmFichier extends CLmObject {  
  // DB Table key
  var $S_FIC_NUMERO = null;
  
  var $_annule = null;

  // DB Fields : see getProps();

  // Filter Fields
  var $_date_min = null;
  var $_date_max = null;
  
  // References
  var $_ref_id = null;
  
	// Behaviour fields
	var $_fix_resend = null;
  
	// Distant field
  var $_consult_id = null;
	var $_resend_fixable = null;
	
	function updateFormFields() {
	  parent::updateFormFields();
	  $this->_annule = $this->S_FIC_ETAT == "?";
	}
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CConsultation';
    $spec->table   = 'S_F_FICHIER';
    $spec->key     = 'S_FIC_NUMERO';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    
    // DB Fields
    $specs["S_FIC_NUMERO"]        = "ref class|CLmFichier";
    $specs["S_FIC_ETAT"]          = "enum list|2|3|4|5|6|7";
    $specs["S_FIC_CPS"]           = "num";
    $specs["S_FIC_DATE"]          = "date";
    $specs["S_FIC_DATE_EMISSION"] = "date";
    $specs["S_FIC_NB_LOT"]        = "num";
    $specs["S_FIC_NB_FSE"]        = "num";
    $specs["S_FIC_NB_TRANS"]      = "num";

    // Distant Fields
    $specs["_resend_fixable"] = "bool";

    return $specs;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["lots"] = "CLmLot S_LOT_FIC";
    return $backProps;
  }
	
	/**
	 * Redefined store for behaviour fields
	 * @return string Store-like message
	 */
	function store() {
		if ($this->_fix_resend) {
			$this->S_FIC_DATE_EMISSION = "";
			$this->S_FIC_NB_TRANS = "0";
      $this->S_FIC_ETAT = "2";
		}
		
		if ($msg = parent::store()) {
			return $msg;
		}
		
		foreach($this->loadBackRefs("lots") as $lot) {
			$lot->S_LOT_ETAT = 4;
			$lot->S_LOT_NB_TRANS = 0;
			if ($msg = $lot->store()) {
				return $msg;
			}
			CAppUI::setMsg("$lot->_class_name-msg-modify");
		}
	}
}

?>