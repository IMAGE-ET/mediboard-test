<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision$
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Thomas Despoix
	*/

CAppUI::requireModuleClass('dmi', 'category_produit_prescriptible');
class CCategoryDM extends CCategoryProduitPrescriptible {
  // DB Table key
  var $category_dm_id = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'category_dm';
    $spec->key   = 'category_dm_id';
    return $spec;
  }
 
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["dms"] = "CDM category_dm_id";
	  return $backProps;
	}
	
  function loadRefsElements() {
    $this->_ref_elements = $this->loadBackRefs("dms", "nom");
  }
  
	function countElements(){
	  $this->_count_elements = $this->countBackRefs("dms");
	}
}

?>