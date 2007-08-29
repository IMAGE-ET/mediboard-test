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
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["malnum"] = "numchar length|6";
    $specs["malnom"] = "str maxLength|20";
    $specs["malpre"] = "str maxLength|10";
    $specs["datnai"] = "numchar length|8";
    
    return $specs;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    if (!is_a($mbObject, "CPatient")) {
      trigger_error("mapping object should be a 'CPatient'");
    }
    
    $this->malnum = str_pad($mbObject->_id % 100000, 6, "0", STR_PAD_LEFT);
    $this->malnom = substr($mbObject->nom, 0, 20);
    $this->malpre = substr($mbObject->prenom, 0, 10);
    $this->datnai = "$mbObject->_jour$mbObject->_mois$mbObject->_annee";
  }

}

?>