<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Alexis Granger
	*/

class CCategoryProduitPrescriptible extends CMbObject {
  // DB fields
  var $nom         = null;
  var $description = null;
  var $group_id    = null;
  
  // Form fields
  var $_count_elements = null;
  
  function getProps() {
  	$props = parent::getProps();
    $props["nom"]         = "str notNull";
    $props["description"] = "text";
    $props["group_id"]    = "ref notNull class|CGroups";
    return $props;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like",
      "description" => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}

?>