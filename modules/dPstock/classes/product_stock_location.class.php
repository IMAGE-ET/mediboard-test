<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductStockLocation extends CMbMetaObject {
  // DB Table key
  var $stock_location_id = null;

  // DB Fields
  var $name              = null;
  var $desc              = null;
	var $position          = null;
  var $group_id          = null;

  // Object References
  var $_ref_group_stocks = null;
  
  /**
   * @var CGroups
   */
	var $_ref_group        = null;
	
	var $_before           = null;
  var $_type             = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_location';
    $spec->key   = 'stock_location_id';
    $spec->uniques["name"] = array("name", "object_class", "object_id");
    return $spec;
  }
	
  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull seekable';
    $specs['desc'] = 'text seekable';
		$specs['position'] = 'num min|1';
    $specs['group_id'] = 'ref notNull class|CGroups';
    $specs['object_class'] = 'enum notNull list|CGroups|CService'; //|CBlocOperatoire
		$specs['_before']  = 'ref class|CProductStockLocation autocomplete|name|true';
    $specs['_type']  = 'str';
    return $specs;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
    $backProps["group_stocks"] = "CProductStockGroup location_id";
    $backProps["service_stocks"] = "CProductStockService location_id";
	  return $backProps;
	}

	function updateFormFields() {
    parent::updateFormFields();
    $this->loadTargetObject(false);
    
    $this->_shortview = ($this->position ? "[".str_pad($this->position, 3, "0", STR_PAD_LEFT)."] " : "") . $this->name;
    $this->_view = ($this->_ref_object ? "{$this->_ref_object->_view} - " : "") . $this->_shortview;
  }
	
	function updateDBfields() {
		parent::updateDBfields();
		
    if ($this->_type) {
      list($this->object_class, $this->object_id) = explode("-", $this->_type);
      $this->_type = null;
    }
    
		if ($this->_before && $this->_before != $this->_id) {
			$next_object = new self;
			$next_object->load($this->_before);
			
			if ($next_object->_id) {
				$query = '';
        $table = $this->_spec->table;
        
				if ($this->position)
					$query = "AND `$table`.`position` BETWEEN $next_object->position AND $this->position";
				else if ($next_object->position)
					$query = "AND `$table`.`position` >= $next_object->position";
				
				$where = array(
			    "`$table`.`position` IS NOT NULL $query",
          "`$table`.`object_class` = '$this->object_class'",
          "`$table`.`object_id` = '$this->object_id'"
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
    else if (!$this->_id && !$this->position) {
      $existing = $this->loadList(null, "position");
      if ($location = end($existing)) 
        $this->position = $location->position + 1;
      else 
        $this->position = 1;
    }
	}
  
  function getStockType(){
    if (!$this->_id) return;
    
    $this->completeField("object_class");
    
    switch ($this->object_class) {
      case "CGroups": 
        return "CProductStockGroup";
      default: 
        return "CProductStockService";
    }
  }
  
  function loadRefsStocks(){
    $ljoin = array(
      "product" => "product_stock_group.product_id = product.product_id",
    );
    $this->loadBackRefs("group_stocks", "product.name", null, null, $ljoin);
    
    if (!empty($this->_back["group_stocks"])) {
      foreach($this->_back["group_stocks"] as $_id => $_stock) {
        if ($_stock->loadRefProduct()->cancelled) unset($this->_back["group_stocks"][$_id]);
      }
    }
    
    $ljoin = array(
      "product" => "product_stock_service.product_id = product.product_id",
    );
    $this->loadBackRefs("service_stocks", "product.name", null, null, $ljoin);
    
    if (!empty($this->_back["service_stocks"])) {
      foreach($this->_back["service_stocks"] as $_id => $_stock) {
        if ($_stock->loadRefProduct()->cancelled) unset($this->_back["service_stocks"][$_id]);
      }
    }
  }

  function loadRefsFwd(){
    $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
  
  function loadRefStock($product_id) {
    $class = $this->getStockType();
    
    $stock = new $class;
    $stock->product_id = $product_id;
    
    switch ($this->object_class) {
      case "CGroups": 
        $stock->group_id = $this->object_id;
      default: 
        $stock->service_id = $this->object_id;
    }
    
    $stock->loadMatchingObject();
    return $stock;
  }
}
?>