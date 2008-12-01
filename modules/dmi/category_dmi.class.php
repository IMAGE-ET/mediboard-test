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
	  return $backRefs;
	}

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}

?>