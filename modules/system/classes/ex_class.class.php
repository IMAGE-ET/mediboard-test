<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClass extends CMbObject {
  var $ex_class_id = null;
  
  var $host_class = null;
  var $event      = null;
  var $name       = null;
  var $disabled   = null;
  
  var $_ref_fields = null;
	var $_ref_host_fields = null;
  var $_ref_constraints = null;
  
  var $_fields_by_name = null;
  var $_host_class_fields = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class";
    $spec->key   = "ex_class_id";
    $spec->uniques["ex_class"] = array("host_class", "event", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["host_class"] = "str notNull protected";
    $props["event"]      = "str notNull protected canonical";
    $props["name"]       = "str notNull seekable";
    $props["disabled"]   = "bool notNull default|1";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fields"]      = "CExClassField ex_class_id";
    $backProps["host_fields"] = "CExClassHostField ex_class_id";
    $backProps["constraints"] = "CExClassConstraint ex_class_id";
    return $backProps;
  }
  
  function load($id = null) {
    if (!($ret = parent::load($id))) {
      return $ret;
    }
    
    global $locales;
    $locales["CExObject_{$this->host_class}_{$this->event}_{$this->_id}"] = $this->_view;
    
    // pas encore oblig� d'utiliser l'eval, mais je pense que ca sera le plus simple
    /*$class_name = "CExObject_{$this->host_class}_{$this->event}_{$this->_id}";
    
    if (!class_exists($class_name)) {
      $table_name = $this->getTableName();
      
      eval("
      class $class_name extends CExObject {
        function getSpec(){
          \$spec = parent::getSpec();
          \$spec->table = '$table_name';
          return \$spec;
        }
      }
      ");
    }*/
    
    return $ret;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = CAppUI::tr($this->host_class) . " - [$this->event] - $this->name";
  }
  
  function loadRefsFields(){
    if (!empty($this->_ref_fields)) return $this->_ref_fields;
    return $this->_ref_fields = $this->loadBackRefs("fields");
  }
  
  function loadRefsHostFields(){
    if (!empty($this->_ref_host_fields)) return $this->_ref_host_fields;
    return $this->_ref_host_fields = $this->loadBackRefs("host_fields");
  }
  
  function loadRefsConstraints(){
    return $this->_ref_constraints = $this->loadBackRefs("constraints");
  }
  
  function getTableName(){
    $this->completeField("host_class", "event");
    return strtolower("ex_{$this->host_class}_{$this->event}_{$this->_id}");
  }
  
  function checkConstraints(CMbObject $object){
    $constraints = $this->loadRefsConstraints();
    
    foreach($constraints as $_constraint) {
      if (!$_constraint->checkConstraint($object)) return false;
    }
    
    return true;
  }
  
  function getAvailableFields(){
    $object = new $this->host_class;
    $this->_host_class_fields = $object->_specs;
		
		foreach($this->_host_class_fields as $_field => $_spec) {
			if ($_field[0] === "_") {
				unset($this->_host_class_fields[$_field]);
			}
		}
		
		return $this->_host_class_fields;
  }
  
  function loadExObjects(CMbObject $object) {
    $ex_object = new CExObject;
    $ex_object->_ex_class_id = $this->_id;
    $ex_object->loadRefExClass();
    $ex_object->setExClass();
    $ex_object->setObject($object);
    $list = $ex_object->loadMatchingList();
    
    foreach($list as $_object) {
      $_object->_ex_class_id = $this->_id;
    }
    
    return $list;
  }
	
	function getGrid($w = 4, $h = 20, $reduce = true){
		$grid = array_fill(0, $h, array_fill(0, $w, array(
		  "type" => null, 
		  "object" => null,
		)));
		
		$out_of_grid = array(
		  "field" => array(), 
		  "label" => array(),
		);
		
		foreach($this->loadRefsFields() as $_ex_field) {
		  $_ex_field->getSpecObject();
			
      $label_x = $_ex_field->coord_label_x;
      $label_y = $_ex_field->coord_label_y;
		  
		  $field_x = $_ex_field->coord_field_x;
		  $field_y = $_ex_field->coord_field_y;
      
      // label
      if ($label_x === null || $label_y === null) {
        $out_of_grid["label"][$_ex_field->name] = $_ex_field;
      }
      else {
        $grid[$label_y][$label_x] = array("type" => "label", "object" => $_ex_field);
      }
		  
		  // field
		  if ($field_x === null || $field_y === null) {
		    $out_of_grid["field"][$_ex_field->name] = $_ex_field;
		  }
		  else {
		    $grid[$field_y][$field_x] = array("type" => "field", "object" => $_ex_field);
		  }
		}
    
    foreach($this->loadRefsHostFields() as $_host_field) {
      $label_x = $_host_field->coord_label_x;
      $label_y = $_host_field->coord_label_y;
			
      $value_x = $_host_field->coord_value_x;
      $value_y = $_host_field->coord_value_y;
      
      // label
      if ($label_x !== null && $label_y !== null) {
        $grid[$label_y][$label_x] = array("type" => "label", "object" => $_host_field);
      }
      
      // value
      if ($value_x !== null && $value_y !== null) {
        $grid[$value_y][$value_x] = array("type" => "value", "object" => $_host_field);
      }
    }
		
		if ($reduce) {
			$max_filled = 0;
			
			foreach($grid as $_y => $_line) {
        $n_filled = 0;
        $x_filled = 0;
				
				foreach($_line as $_x => $_cell) {
					if ($_cell !== array("type" => null, "object" => null)) {
					  $n_filled++;
						$x_filled = max($_x, $x_filled);
					}
				}
				
				if ($n_filled == 0) unset($grid[$_y]);
				
        $max_filled = max($max_filled, $x_filled);
			}
      
      foreach($grid as $_y => $_line) {
      	$grid[$_y] = array_slice($_line, 0, $max_filled+1);
      }
		}
		
		return array($grid, $out_of_grid, "grid" => $grid, "out_of_grid" => $out_of_grid);
	}
  
  function store(){
    if ($msg = $this->check()) return $msg;
    
    if (!$this->_id) {
      if ($msg = parent::store()) {
        return $msg;
      }
      
      $table_name = $this->getTableName();
      $query = "CREATE TABLE `$table_name` (
        `ex_object_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `object_id` INT UNSIGNED NOT NULL,
        `object_class` VARCHAR(80) NOT NULL,
        INDEX ( `object_id` ),
        INDEX ( `object_class` )
      ) /*! ENGINE=MyISAM */;";
      
      $ds = $this->_spec->ds;
      if (!$ds->query($query)) {
        return "La table '$table_name' n'a pas pu �tre cr��e (".$ds->error().")";
      }
    }
    
    else if ($this->fieldModified("event")) {
      $table_name_old = $this->_old->getTableName();
      $table_name     = $this->getTableName();
      $query = "ALTER TABLE `$table_name_old` RENAME `$table_name`";
      
      $ds = $this->_spec->ds;
      if (!$ds->query($query)) {
        return "La table '$table_name' n'a pas pu �tre renomm�e (".$ds->error().")";
      }
    }
    
    return parent::store();
  }
  
  function delete(){
    if ($msg = $this->canDeleteEx()) return $msg;
    
    // suppression des objets des champs sans supprimer les colonnes de la table
    $fields = $this->loadBackRefs("fields");
    foreach($fields as $_field) {
      $_field->_dont_drop_column = true;
      $_field->delete();
    }
    
    $table_name = $this->getTableName();
    $query = "DROP TABLE `$table_name`";
    
    $ds = $this->_spec->ds;
    if (!$ds->query($query)) {
      return "La table '$table_name' n'a pas pu �tre supprim�e (".$ds->error().")";
    }
    
    return parent::delete();
  }
}
