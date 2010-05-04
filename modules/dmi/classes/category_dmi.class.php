<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass('dmi', 'category_produit_prescriptible');

class CDMICategory extends CCategoryProduitPrescriptible {
  // DB Table key
  var $category_id = null;
  
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