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
  var $conditional= null;
  var $required   = null;
  //var $unicity    = null;
  
  var $_ref_fields = null;
  var $_ref_constraints = null;
  var $_ref_groups = null;
  
  var $_fields_by_name = null;
  var $_host_class_fields = null;
  var $_host_class_options = null;
  
  static $_extendable_classes = array(
    "CPrescriptionLineElement",
    "CPrescriptionLineMedicament",
    "COperation",
    "CAdministration",
  );

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
    $props["conditional"]= "bool notNull default|0";
    $props["required"]   = "bool notNull default|0";
    //$props["unicity"]    = "enum notNull list|host|ref1|ref2 default|host";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["field_groups"] = "CExClassFieldGroup ex_class_id";
    $backProps["constraints"]  = "CExClassConstraint ex_class_id";
    $backProps["ex_triggers"]  = "CExClassFieldTrigger ex_class_triggered_id";
    return $backProps;
  }
  
  function getHostClassOptions(){
    if (!$this->host_class || !$this->event || $this->event === "void") return;
    
    $object = new $this->host_class;
    return $this->_host_class_options = $object->_spec->events[$this->event];
  }
  
  function getLatestExObject(CMbObject $object, $level = 1){
    $ex_object = new CExObject;
    $ex_object->_ex_class_id = $this->_id;
    $ex_object->setExClass();

    if ($level == 1) {
      $where = array(
        "reference_class" => "= '$object->_class_name'",
        "reference_id"    => "= '$object->_id'",
      );
    }
    else {
      $where = array(
        "reference2_class" => "= '$object->_class_name'",
        "reference2_id"    => "= '$object->_id'",
      );
    }
    
    $ex_object->loadObject($where, "ex_object_id DESC");
    $ex_object->load(); // needed !!!!!!!

    return $ex_object;
  }
  
  function resolveReferenceObject(CMbObject $object, $level = 1){
    $options = $this->getHostClassOptions();
    list($ref_class, $path) = CValue::read($options, "reference$level");
    
    if (!$path) return;
    
    $parts = explode(".", $path);
    
    $reference = $object;
    foreach($parts as $_fwd) {
      $reference = $reference->loadFwdRef($_fwd);
    }
    
    return $reference;
  }
  
  function load($id = null) {
    if (!($ret = parent::load($id))) {
      return $ret;
    }
    
    global $locales;
    $locales[$this->getExClassName()] = $this->_view;
    
    // pas encore obligé d'utiliser l'eval, mais je pense que ca sera le plus simple
    /*$class_name = "CExObject_{$this->_id}";
    
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
  
  function getExClassName(){
    return "CExObject_{$this->_id}";
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    if ($this->host_class === "CMbObject") {
      $this->_view = "Non classé";
    }
    else {
      $this->_view = CAppUI::tr($this->host_class) . " - [$this->event]";
    }
    
    $this->_view .= " - $this->name";
  }
  
  function loadRefsGroups(){
    if (!empty($this->_ref_groups)) return $this->_ref_groups;
    return $this->_ref_groups = $this->loadBackRefs("field_groups", "ex_class_field_group_id");
  }
  
  function loadRefsAllFields($cache = false){
    $groups = $this->loadRefsGroups();
    $fields = array();
    foreach($groups as $_group) {
      $_fields = $_group->loadRefsFields($cache);
      $fields = array_merge($_fields, $fields);
    }
    return $fields;
  }
  
  function loadRefsConstraints(){
    return $this->_ref_constraints = $this->loadBackRefs("constraints");
  }
  
  function getTableName(){
    return "ex_object_{$this->_id}";
  }
  
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if ($this->fieldModified("host_class")) {
    	$count_constraints = $this->countBackRefs("constraints");
    	if ($count_constraints > 0) {
    		return "Impossible de changer le type d'objet hôte de ce formulaire car il possède $count_constraints contrainte(s)";
    	}
			
      $groups = $this->loadRefsGroups();
      foreach($groups as $_group) {
        if ($_group->countBackRefs("host_fields")) {
          $old_class = $this->_old->host_class;
          return "Impossible de changer le type d'objet hôte de ce formulaire car il comporte
           des champs de <strong>".CAppUI::tr($old_class)."</strong> dans la grille de disposition";
        }
      }
    }
  }
  
  // constraint1 OR constraint2 OR ...
  function checkConstraints(CMbObject $object){
    $constraints = $this->loadRefsConstraints();
    
    if (empty($constraints)) {
      return true;
    }
    
    foreach($constraints as $_constraint) {
      if ($_constraint->checkConstraint($object)) return true;
    }
    
    return false;
  }
  
  function getAvailableFields(){
    $object = new $this->host_class;
    $this->_host_class_fields = $object->_specs;
    
    foreach($this->_host_class_fields as $_field => $_spec) {
      if ($_field == $object->_spec->key) {
        unset($this->_host_class_fields[$_field]);
        continue;
      }
      
      /*if ($_spec instanceof CRefSpec && $_spec->meta) {
        unset($this->_host_class_fields[$_spec->meta]);
        continue;
      }*/
      
      // LEVEL 1
      if ($_field[0] === "_" || // form field
          !($_spec->show === null || $_spec->show == 1) || // not shown
          $_spec instanceof CRefSpec && $_spec->meta && !$this->_host_class_fields[$_spec->meta] instanceof CEnumSpec // not a finite meta class field
          ) {
        unset($this->_host_class_fields[$_field]);
        continue;
      }
      
			// LEVEL 2
      if ($_spec instanceof CRefSpec) {
      	
				// LEVEL 2 + Class list
      	if ($_spec->meta && $this->_host_class_fields[$_spec->meta] instanceof CEnumSpec) {
      		unset($this->_host_class_fields[$_field]);
					
	      	// boucle sur les classes du enum
					$classes = $this->_host_class_fields[$_spec->meta]->_list;
					
					foreach($classes as $_class) {
						$_key = "$_field.$_class";
		        
		        $_target = new $_class;
            
            $this->_host_class_fields[$_key] = new CRefSpec($this->host_class, $_field, "ref class|$_class");
            $this->_host_class_fields[$_key]->_subspecs = array();
		        
		        foreach($_target->_specs as $_subfield => $_subspec) {
		          if (!$_subfield || $_subfield === $_target->_spec->key) continue;
		          
		          if ($_subfield[0] === "_" || // form field
		              !($_subspec->show === null || $_subspec->show == 1) || // not shown
		              $_subspec instanceof CRefSpec && $_subspec->meta && !$_target->_specs[$_subspec->meta] instanceof CEnumSpec // not a finite meta class field
		              ) {
		            continue;
		          }
		          
		          $this->_host_class_fields[$_key]->_subspecs[$_subfield] = $_subspec;
		        }
					}
				}
				
				// LEVEL 2 + Single class
				else {
          $_key = $_field;
          $this->_host_class_fields[$_key]->_subspecs = array();
          
          $_class = $_spec->class;
          if (!$_class) continue;
          
          $_target = new $_class;
          
          foreach($_target->_specs as $_subfield => $_subspec) {
            if (!$_subfield || $_subfield === $_target->_spec->key) continue;
						
            if ($_subfield[0] === "_" || // form field
                !($_subspec->show === null || $_subspec->show == 1) || // not shown
                $_subspec instanceof CRefSpec && $_subspec->meta && isset($object->_specs[$_subspec->meta]) && !$object->_specs[$_subspec->meta] instanceof CEnumSpec // not a finite meta class field
                ) {
              continue;
            }
            
            $this->_host_class_fields[$_key]->_subspecs[$_subfield] = $_subspec;
          }
				}
      }
    }
    
    return $this->_host_class_fields;
  }
	
	function buildHostFieldsList() {
		$this->getAvailableFields();
		
		$list = array();
		foreach($this->_host_class_fields as $_field => $_spec) {
		  $element = array(
		    "prop"  => $_spec,
		    "title" => null,
        "view"  => null,
        "longview"  => null,
		    "type"  => null,
		    "level" => 0,
		  );
		  
		  $_subfield = explode(".", $_field);
		  
		  // Level 1 title
		  if ($_spec instanceof CRefSpec && $_spec->class) {
		    if ($_spec->meta) {
		      $_meta_spec = $this->_host_class_fields[$_spec->meta];
		      $element["type"] = implode(" OU ", $_meta_spec->_locales);
		    }
		    else {
		      $element["type"] = CAppUI::tr($_spec->class);
		    }
		  }
		  else {
		    $element["type"] = CAppUI::tr("CMbFieldSpec.type.".$_spec->getSpecType());
		  }
		  
		  // Level 1 type
		  if (count($_subfield) > 1) {
        $element["title"] = CAppUI::tr("$this->host_class-$_subfield[0]")." de type ".CAppUI::tr("$_subfield[1]");
        $element["longview"] = CAppUI::tr("$this->host_class-$_subfield[0]-desc")." de type ".CAppUI::tr("$_subfield[1]");
		  }
		  else {
		    $element["title"] = CAppUI::tr("$this->host_class-$_field");
        $element["longview"] = CAppUI::tr("$this->host_class-$_field-desc");
		  }
		  
		  $element["view"] = $element["title"];
		  $parent_view = $element["view"];
		  
		  $list[$_field] = $element;
		  
		  // Level 2
		  if ($_spec instanceof CRefSpec) {
		    foreach ($_spec->_subspecs as $_key => $_subspec) {
		      $_subfield = explode(".", $_key);
		      $_subfield = reset($_subfield);
		      
		      $element = array(
		        "prop"  => $_subspec,
		        "title" => null,
		        "type"  => null,
		        "level" => 1,
		      );
		      
		      if ($_subspec instanceof CRefSpec && $_subspec->class) {
		        if ($_subspec->meta) {
		          //$_meta_spec = $ex_class->_host_class_fields[$_spec->meta];
		          //$element["type"] = implode(" OU ", $_meta_spec->_locales);
		        }
		        else {
		          $element["type"] = CAppUI::tr("$_subspec->class");
		        }
		      }
		      else {
		        $element["type"] = CAppUI::tr("CMbFieldSpec.type.".$_subspec->getSpecType());
		      }
		      
		      $element["view"] = $parent_view." / ".CAppUI::tr("$_subspec->className-$_subfield");
          $element["longview"] = $parent_view." / ".CAppUI::tr("$_subspec->className-$_subfield-desc");
          $element["title"] = " |- ".CAppUI::tr("$_subspec->className-$_subfield");
		      
		      $list["$_field-$_key"] = $element;
		    }
		  }
		}
		
		return $list;
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
      $_object->setExClass();
    }
    
    return $list;
  }
  
  static function getTree(){
    $ex_class = new self;
    $list_ex_class = $ex_class->loadList(null, "host_class, event, name");
    
    $class_tree = array();
    
    foreach($list_ex_class as $_ex_class) {
      $host_class = $_ex_class->host_class;
      $event = $_ex_class->event;
      
      if (!isset($class_tree[$host_class])) {
        $class_tree[$host_class] = array();
      }
      
      if (!isset($class_tree[$host_class][$event])) {
        $class_tree[$host_class][$event] = array();
      }
      
      $class_tree[$host_class][$event][] = $_ex_class;
    }
    
    if (isset($class_tree["CMbObject"])) {
      $not_sorted = $class_tree["CMbObject"];
      unset($class_tree["CMbObject"]);
      $class_tree["CMbObject"] = $not_sorted;
    }
    
    return $class_tree;
  }
  
  function getGrid($w = 4, $h = 20, $reduce = true) {
    $big_grid = array();
    $big_out_of_grid = array();
    $groups = $this->loadRefsGroups();
    
    foreach($groups as $_ex_group) {
      $grid = array_fill(0, $h, array_fill(0, $w, array(
        "type" => null, 
        "object" => null,
      )));
      
      $out_of_grid = array(
        "field" => array(), 
        "label" => array(),
        "message_title" => array(),
        "message_text" => array(),
      );
      
      /**
       * @var CExClassFieldGroup
       */
      $_ex_group;
      
      $_ex_group->loadRefsFields();
      
      foreach($_ex_group->_ref_fields as $_ex_field) {
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
    
      // Host fields
      $_ex_group->loadRefsHostFields();
      foreach($_ex_group->_ref_host_fields as $_host_field) {
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
    
      // Messages
      $_ex_group->loadRefsMessages();
      foreach($_ex_group->_ref_messages as $_message) {
        $title_x = $_message->coord_title_x;
        $title_y = $_message->coord_title_y;
        
        $text_x = $_message->coord_text_x;
        $text_y = $_message->coord_text_y;
        
        // label
        if ($title_x === null || $title_y === null) {
          $out_of_grid["message_title"][$_message->_id] = $_message;
        }
        else {
          $grid[$title_y][$title_x] = array("type" => "message_title", "object" => $_message);
        }
        
        // value
        if ($text_x === null || $text_y === null) {
          $out_of_grid["message_text"][$_message->_id] = $_message;
        }
        else {
          $grid[$text_y][$text_x] = array("type" => "message_text", "object" => $_message);
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
        
        if (empty($out_of_grid)) {
          foreach($grid as $_y => $_line) {
            $grid[$_y] = array_slice($_line, 0, $max_filled+1);
          }
        }
      }
      
      $big_grid       [$_ex_group->_id] = $grid;
      $big_out_of_grid[$_ex_group->_id] = $out_of_grid;
    }
    
    return array(
      $big_grid, $big_out_of_grid, $groups,
      "grid"        => $big_grid, 
      "out_of_grid" => $big_out_of_grid, 
      "groups"      => $groups,
    );
  }
  
  function store(){
    if ($msg = $this->check()) return $msg;
    
    $is_new = !$this->_id;
    
    if ($is_new) {
      if ($msg = parent::store()) {
        return $msg;
      }
      
      // Groupe par défaut
      $ex_group = new CExClassFieldGroup;
      $ex_group->name = "Groupe général";
      $ex_group->ex_class_id = $this->_id;
      $ex_group->store();
      
      $table_name = $this->getTableName();
      $query = "CREATE TABLE `$table_name` (
        `ex_object_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `object_id` INT UNSIGNED NOT NULL,
        `object_class` VARCHAR(80) NOT NULL,
        `reference_id` INT UNSIGNED NOT NULL,
        `reference_class` VARCHAR(80) NOT NULL,
        `reference2_id` INT UNSIGNED NOT NULL,
        `reference2_class` VARCHAR(80) NOT NULL,
        INDEX ( `object_id` ),
        INDEX ( `object_class` )
      ) /*! ENGINE=MyISAM */;";
      
      $ds = $this->_spec->ds;
      if (!$ds->query($query)) {
        return "La table '$table_name' n'a pas pu être créée (".$ds->error().")";
      }
    }
    
    return parent::store();
  }
  
  function delete(){
    if ($msg = $this->canDeleteEx()) return $msg;
    
    // suppression des objets des champs sans supprimer les colonnes de la table
    $fields = $this->loadRefsAllFields();
    foreach($fields as $_field) {
      $_field->_dont_drop_column = true;
      $_field->delete();
    }
    
    $table_name = $this->getTableName();
    $query = "DROP TABLE `$table_name`";
    
    $ds = $this->_spec->ds;
    if (!$ds->query($query)) {
      return "La table '$table_name' n'a pas pu être supprimée (".$ds->error().")";
    }
    
    return parent::delete();
  }
}
