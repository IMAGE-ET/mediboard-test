<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Thomas Despoix
	*/

CAppUI::requireModuleClass('dmi', 'category_produit_prescriptible');
class CDMICategory extends CCategoryProduitPrescriptible {
  // DB Table key
  var $category_id = null;

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
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["dmis"] = "CDMI category_id";
	  return $backProps;
	}
	
	function countElements(){
	  $this->_count_elements = $this->countBackRefs("dmis");
	}
	
  function loadRefsElements() {
    $this->_ref_elements = $this->loadBackRefs("dmis", "nom");
  }
}

?>