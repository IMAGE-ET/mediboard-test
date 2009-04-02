<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductStockLocation extends CMbObject {
  // DB Table key
  var $stock_location_id = null;

  // DB Fields
  var $name              = null;
  var $desc              = null;
	var $position          = null;
	var $group_id          = null;

  // Object References
  var $_ref_group_stocks = null;
	var $_ref_group        = null;
	
	var $_before           = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_location';
    $spec->key   = 'stock_location_id';
    return $spec;
  }
	
  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull';
    $specs['desc'] = 'text';
		$specs['position'] = 'num min|1';
		$specs['group_id'] = 'ref notNull class|CGroups';
		$specs['_before']  = 'ref class|CProductStockLocation autocomplete|name|true';
    return $specs;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["group_stocks"] = "CProductStockGroup location_id";
	  return $backProps;
	}

	function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "[$this->position] $this->name";
  }
	
	function updateDBfields() {
		parent::updateDBfields();
		
		if ($this->_before && $this->_before != $this->_id) {
			$next_object = new self;
			$next_object->load($this->_before);
			
			if ($next_object->_id) {
				$query = '';
				if ($this->position)
					$query = ' AND `position` BETWEEN '.$next_object->position.' AND '.$this->position;
				else if ($next_object->position)
					$query = ' AND `position` >= '.$next_object->position;
				
				$where = array(
			    '`position` IS NOT NULL'.$query
				);
				
				$this->position = $next_object->position;
				$next_objects = $this->loadList($where);
	      foreach($next_objects as &$object) {
	        $object->position++;
	        $object->store();
	      }

				if (count($next_objects) == 0) {
					$next_object->position = 2;
					$next_object->store();
					$this->position = 1;
				}
			}

			$this->_before = null;
		}
	}

  function loadRefsFwd(){
    $this->_ref_group = new CGroups;
    $this->_ref_group = $this->_ref_group->getCached($this->group_id);
  }
}
?>