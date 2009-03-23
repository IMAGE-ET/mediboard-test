<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 2249 $
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
  
  // Distant field
  var $_consult_id = null;

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
    $specs["S_FIC_ETAT"]          = "enum list|4|5";
    $specs["S_FIC_CPS"]           = "num";
    $specs["S_FIC_DATE"]          = "date";
    $specs["S_FIC_DATE_EMISSION"] = "date";
    $specs["S_FIC_NB_LOT"]        = "num";
    $specs["S_FIC_NB_FSE"]        = "num";
    $specs["S_FIC_NB_TRANS"]      = "num";

    // Filter Fields
    $specs["_date_min"] = "date";
    $specs["_date_max"] = "date moreThan|_date_min";
    
    return $specs;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["lots"] = "CLmLot S_LOT_FIC";
    return $backProps;
  }
}

?>