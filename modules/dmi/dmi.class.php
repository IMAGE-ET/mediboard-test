<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Thomas Despoix
	*/

class CDMI extends CMbObject {
// DB Table key
  var $dmi_id  = null;

  // DB fields
  var $nom         	= null;
  var $en_lot		= null;
  var $description	= null;
  var $reference	= null;
  var $lot	= null;
  var $dans_livret	= null;
  
  var $category_id	= null;
  
function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dmi';
    $spec->key   = 'dmi_id';
    return $spec;
  }
  
 function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["nom"]			= "notNull str";
    $specs["en_lot"]			= "bool";
    $specs["description"]	= "text";
    $specs["reference"]		= "notNull str";
    $specs["lot"]			= "str";
    $specs["dans_livret"]	= "bool";
    $specs["category_id"]= "notNull ref class|CDMICategory";
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