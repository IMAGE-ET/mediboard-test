<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage 
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * Description
 */
class ClassName extends CMbObject {
	/**
	 * Table Key
	 * @var integer
	 */
	var $table_name_id = null;
	
	/**
	 * Description
	 * @var 
	 */
	var $field_name = null;
	
	/**
	 * Description
	 * @var 
	 */
	var $class_ref_id = null;
	
	/**
	 * Description
	 * @var 
	 */
	var $_ref_class = null;
	
	/**
	 * Initialize the class specifications
	 * @return CMbFieldSpec
	 */
	function getSpec() {
		$spec = parent::getSpec();
		$spec->table	= "table_name";
		$spec->key		= "table_name_id";
		return $spec;	
	}
	
	/**
	 * Get backward reference specifications
	 * @return array
	 */
	function getBackProps() {
		$backProps = parent::getBackProps();
		$backProps["backRefClass"] = "backRefClass dbReferenceField";
		return $backProps;
	}
	
	/**
	 * Get the properties of our class as string
	 * @return array
	 */
	function getProps() {
		$props = parent::getProps();
		$props["field_name"] 	= "spec properties";
		$props["class_ref_id"] = "ref notNull class|ref_class_name";
		return $props;
	}
	
	/**
	 * Description
	 * @param boolean $cache Use the object cache, or not. Default true
	 * @return CMbObject
	 */
	function loadRef($cache = true) {
		return $this->_ref_class = $this->loadFwdRef("class_ref_id", $cache);
	}
}
?>