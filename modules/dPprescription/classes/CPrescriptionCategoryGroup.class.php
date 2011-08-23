<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionCategoryGroup extends CMbObject {
  // DB Table key
  var $prescription_category_group_id = null;
  
  // DB Fields
	var $libelle = null;
  var $group_id = null;

	var $_ref_category_group_items = null; 

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_category_group';
    $spec->key   = 'prescription_category_group_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
		$specs["libelle"] = "str notNull";
    $specs["group_id"] = "ref class|CGroups";
		return $specs;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["prescription_category_group_items"]   = "CPrescriptionCategoryGroupItem prescription_category_group_id";
    return $backProps;
  }
	
	function loadRefsCategoryGroupItems(){
		$this->_ref_category_group_items = $this->loadBackRefs("prescription_category_group_items");
	}
}

?>