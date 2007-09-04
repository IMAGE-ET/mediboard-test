<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("sherpa", "spObject"));

/**
 * Classe du malade sherpa
 */
class CSpMalade extends CSpObject {  
  // DB Table key
  var $malnum = null;

  // DB Fields
  var $malnom = null;
  var $malpre = null;
  var $datnai = null;
  
	function CSpMalade() {
		$this->CMbObject("t_malade", "malnum");    
    $this->loadRefModule(basename(dirname(__FILE__)));
 	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CPatient";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["malnum"] = "numchar length|6";
    $specs["malnom"] = "str maxLength|20";
    $specs["malpre"] = "str maxLength|10";
    $specs["datnai"] = "str length|10";
    
    return $specs;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $patient = $mbObject;
        
    $this->malnum = str_pad($this->loadLatestId()+1, 6, "0", STR_PAD_LEFT);
    $this->malnom = $this->makeString($patient->nom, 20);
    $this->malpre = $this->makeString($patient->prenom, 10);
    $this->datnai = $patient->_naissance;
  }

  function loadLatestId() {
    $ds =& $this->_spec->ds;
    $query = "SELECT MAX(`$this->_tbl_key`) FROM `$this->_tbl`";
    return $ds->loadResult($query);
  }  
}

?>