<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/


/**
 * Classe du malade sherpa
 */
class CSpMalade extends CSpObject {  
  // DB Table key
  var $malade_id = null;

  // DB Fields
  var $malnum = null;
  var $malnom = null;
  var $malpre = null;
  var $datnai = null;
  
	function CSpMalade() {
		$this->CMbObject("t_malade", "malade_id");    
    $this->loadRefModule(basename(dirname(__FILE__)));
 	}
  
  function getSpecs() {
    $specs = parent::getSpecs();

    $specs["malnum"] = "numchar length|6";
    $specs["malnom"] = "str length|20";
    $specs["malpre"] = "str length|10";
    $specs["datnai"] = "numchar length|8";
    
    return $specs;
  }
  
  function getSeeks() {
    $seeks = parent::getSeeks();
    return $seeks;
  }

  function getHelpedFields(){
    $helpers = parent::getHelpedFields();
    return $helpers;
  }
}

?>