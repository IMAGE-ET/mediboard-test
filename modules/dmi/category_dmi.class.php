<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Thomas Despoix
	*/

class CDMICategory extends CMbObject {
  // DB Table key
  var $category_id = null;

  // DB fields
  var $nom         = null;
  var $description = null;
  var $group_id    = null;
  
  // Form Fields
  var $_count_dmis = null;
  
  // Collections
  var $_ref_dmis = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dmi_category';
    $spec->key   = 'category_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["nom"]         = "notNull str";
    $specs["description"] = "text";
    $specs["group_id"]    = "notNull ref class|CGroups";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like",
      "description" => "like"
    );
  }
  
	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["dmis"] = "CDMI category_id";
	  return $backRefs;
	}
	
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefsDMI() {
    $this->_ref_dmis = $this->loadBackRefs("dmis", "nom");
  }
  
  function countRefsDMI() {
    return $this->_count_dmis = $this->countBackRefs("dmis");
  }
}

?>