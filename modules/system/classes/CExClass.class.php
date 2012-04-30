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
  var $unicity    = null;
  var $group_id   = null;
  
  var $_ref_fields = null;
  var $_ref_constraints = null;
  var $_ref_groups = null;
  
  var $_fields_by_name = null;
  var $_host_class_fields = null;
  var $_host_class_options = null;
  
  static $_groups_cache = array();
  
  static $_list_cache = array();
  
  static $_extendable_classes = array(
    "CPrescriptionLineElement",
    "CPrescriptionLineMedicament",
    "COperation",
    "CSejour",
    "CConsultation",
    "CConsultAnesth",
    "CAdministration",
  );
  
  static function getExtendableSpecs(){
    $classes = self::$_extendable_classes;
    $specs = array();
    
    foreach($classes as $_class) {
      if (!class_exists($_class)) {
        continue;
      }
      
      $instance = new $_class;
      if (!empty($instance->_spec->events) && $instance->_ref_module && $instance->_ref_module->mod_active) {
        $specs[$_class] = $instance->_spec->events;
      }
    }
      
    return $specs;
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class";
    $spec->key   = "ex_class_id";
    $spec->uniques["ex_class"] = array("group_id", "host_class", "event", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["host_class"] = "str notNull protected";
    $props["event"]      = "str notNull protected canonical";
    $props["name"]       = "str notNull seekable";
    $props["disabled"]   = "bool notNull default|1";
    $props["conditional"]= "bool notNull default|0";
    $props["required"]   = "bool default|0";
    $props["unicity"]    = "enum notNull list|no|host default|no vertical"; //"enum notNull list|no|host|reference1|reference2 default|no vertical";
    $props["group_id"]   = "ref class|CGroups";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["field_groups"] = "CExClassFieldGroup ex_class_id";
    $backProps["constraints"]  = "CExClassConstraint ex_class_id";
    $backProps["ex_triggers"]  = "CExClassFieldTrigger ex_class_triggered_id";
    return $backProps;
  }
  
  function getExportedBackRefs(){
    $export = parent::getExportedBackRefs();
    $export["CExClass"]           = array("field_groups", /*"constraints", */"ex_triggers");
    $export["CExClassFieldGroup"] = array("class_fields", "host_fields", "class_messages");
    $export["CExConcept"]         = array("list_items");
    $export["CExList"]            = array("list_items");
    return $export;
  }
  
  function getHostClassOptions(){
    if (!$this->host_class || !$this->event || $this->event === "void") return;
    
    $object = new $this->host_class;
    return $this->_host_class_options = $object->_spec->events[$this->event];
  }
  
  function getWhereConceptSearch($search) {
    $comp_map = array(
      "eq"  => "=",
      "lte" => "<=",
      "lt"  => "<",
      "gte" => ">=",
      "gt"  => ">",
    );
          
    $_fields = $this->loadRefsAllFields();
    $_table  = $this->getTableName();
    $where   = array();
    
    foreach($_fields as $_field) {
      if (!isset($search[$_field->concept_id])) {
        continue;
      }
      
      $_ds    = $_field->_spec->ds;
      $_col   = "$_table.$_field->name";
      
      foreach($search[$_field->concept_id] as $_i => $_val) {
        $_val_a = $_val['a'];
        $_comp  = $_val['comp'];
        
        if (isset($comp_map[$_comp])) {
          $where[$_col] = $_ds->prepare($comp_map[$_comp]."%", $_val_a);
        }
        else {
          switch($_comp) {
            case "contains": 
              $where[$_col] = $_ds->prepareLike("%$_val_a%");
              break;
            case "begins": 
              $where[$_col] = $_ds->prepareLike("$_val_a%");
              break;
            case "ends": 
              $where[$_col] = $_ds->prepareLike("%$_val_a");
              break;
            case "between": 
              $_val_b = $_val['b'];
              $where[$_col] = $_ds->prepare("BETWEEN % AND %", $_val_a, $_val_b);
              break;
          }
        }
      }
    }

    return $where;
  }
  
  /**
   * Returns an instance of CExObject which corresponds to the unicity
   * @param CMbObject $host
   * @return CExObject
   */
  function getExObjectForHostObject(CMbObject $host) {
    if (!$host->_id) {
      return array();
    }
    
    $this->completeField("disabled", "unicity");
    
    $existing = $this->loadExObjects($host, $ex_object);
    $disabled = $this->disabled || !$this->checkConstraints($host);
    $level = 1;
    
    switch($this->unicity) {
      case "no":
        if (!$disabled) {
          array_unshift($existing, $ex_object);
        }
        
        return $existing;
        
      case "host":
        if (count($existing)) {
          return $existing;
        }
        
        if (!$disabled) {
          return array($ex_object);
        }
        /*
      case "reference2": $level++;
      case "reference1": 
        $reference_object = $this->resolveReferenceObject($host, $level);
        return array($this->getLatestExObject($reference_object, $level));*/
    }
    
    return array();
  }
  
  function getExObjectInstance(){
    $ex_object = new CExObject;
    $ex_object->_ex_class_id = $this->_id;
    $ex_object->setExClass();
    return $ex_object;
  }
  
  function getLatestExObject(CMbObject $object, $level = 1){
    $ex_object = $this->getExObjectInstance();

    switch($level) {
      case 1:
        $where = array(
          "reference_class" => "= '$object->_class'",
          "reference_id"    => "= '$object->_id'",
        );
        break;
        
      case 2:
        $where = array(
          "reference2_class" => "= '$object->_class'",
          "reference2_id"    => "= '$object->_id'",
        );
        break;
        
      case "host":
        $where = array(
          "object_class" => "= '$object->_class'",
          "object_id"    => "= '$object->_id'",
        );
        break;
    }
    
    $ex_object->loadObject($where, "ex_object_id DESC");
    $ex_object->load(); // needed !!!!!!!

    return $ex_object;
  }
  
  function resolveReferenceObject(CMbObject $object, $level = 1){
    $options = $this->getHostClassOptions();
    list($ref_class, $path) = CValue::read($options, "reference$level");
    
    if (!$object->_id) {
      return new $ref_class;
    }
    
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
    /*$class = "CExObject_{$this->_id}";
    
    if (!class_exists($class)) {
      $table_name = $this->getTableName();
      
      eval("
      class $class extends CExObject {
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
      $this->_view = CAppUI::tr($this->host_class) . " - [".CAppUI::tr("$this->host_class-event-$this->event")."]";
    }
    
    $this->_view .= " - $this->name";
  }
  
  function loadRefsGroups($cache = true){
    if ($cache && isset(self::$_groups_cache[$this->_id])) {
      return $this->_ref_groups = self::$_groups_cache[$this->_id];
    }
    
    $this->_ref_groups = $this->loadBackRefs("field_groups", "ex_class_field_group_id");
    
    if ($cache) {
      self::$_groups_cache[$this->_id] = $this->_ref_groups;
    }
    
    return $this->_ref_groups;
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
  
  static function getHostObjectSpecs($object) {
    $specs = array();
    $specs["CONNECTED_USER"] = CMbFieldSpecFact::getSpec(CMediusers::get(), "CONNECTED_USER", "ref class|CMediusers show|1");;
    return array_merge($specs, $object->_specs);
  }
  
  function getAvailableFields(){
    $object = new $this->host_class;
    
    $this->_host_class_fields = self::getHostObjectSpecs($object);
    
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
      if (($_field[0] === "_" && ($_spec->show === null || $_spec->show == 0)) || // form field
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
        "field" => null,
      );
      
      $host_class = $this->host_class;
      
      if ("CONNECTED_USER" === $_field) {
        $host_class = "CMediusers";
      }
      
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
        $element["title"] = CAppUI::tr("$host_class-$_subfield[0]")." de type ".CAppUI::tr("$_subfield[1]");
        $element["longview"] = CAppUI::tr("$host_class-$_subfield[0]-desc")." de type ".CAppUI::tr("$_subfield[1]");
        $element["field"] = "$host_class-$_subfield[0]";
      }
      else {
        $element["title"] = CAppUI::tr("$host_class-$_field");
        $element["longview"] = CAppUI::tr("$host_class-$_field-desc");
        $element["field"] = "$host_class-$_field";
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
          $element["field"] = "$_subspec->className-$_subfield";
          
          $list["$_field-$_key"] = $element;
        }
      }
    }
    
    return $list;
  }
  
  function loadExObjects(CMbObject $object, &$ex_object = null) {
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
  
  function canCreateNew(CMbObject $host) {
    switch($this->unicity) {
      default:
      case "no":
        return true;
        
      case "host":
        $ex_object = new CExObject;
        $ex_object->_ex_class_id = $this->_id;
        $ex_object->loadRefExClass();
        $ex_object->setExClass();
        $ex_object->setObject($host);
        return $ex_object->countMatchingList() == 0;
        
        /*
      case "reference2": $level++;
      case "reference1": 
        $reference_object = $this->resolveReferenceObject($host, $level);
        return array($this->getLatestExObject($reference_object, $level));*/
    }
  }
  
  static function getTree(){
    $group_id = CGroups::loadCurrent()->_id;
    $where = array(
      "group_id IS NULL OR group_id = '$group_id'"
    );
    
    $ex_class = new self;
    $list_ex_class = $ex_class->loadList($where, "host_class, event, name");
    
    $class_tree = array(
      "CMbObject" => array(),
    );
    
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
    
    if (empty($class_tree["CMbObject"])) {
      unset($class_tree["CMbObject"]);
    }
    
    return $class_tree;
  }
  
  static function getExClassesForObject($object, $event, $type = "required", $exclude_ex_class_ids = array()) {
    if (is_string($object)) {
      $object = CMbObject::loadFromGuid($object);
    }
    
    if ($type == "required" && !CValue::read($object->_spec->events[$event], "auto", false)) {
      return array();
    }
    
    $ex_class = new self;
    
    $group_id = CGroups::loadCurrent()->_id;
    $ds = $ex_class->_spec->ds;
    
    $where = array(
      "host_class"  => $ds->prepare("=%", $object->_class),
      "event"       => $ds->prepare("=%", $event),
      "disabled"    => $ds->prepare("=%", 0),
      "conditional" => $ds->prepare("=%", 0),
      "group_id"    => $ds->prepare("=% OR group_id IS NULL", $group_id),
    );
    
    switch($type) {
      //case "required":    $where["required"] = 1;    break;
      case "disabled":    $where["disabled"] = 1;    break;
      case "conditional": $where["conditional"] = 1; break;
    }
    
    $ex_classes = $ex_class->loadList($where);
    
    foreach($ex_classes as $_id => $_ex_class) {
      if (isset($exclude_ex_class_ids[$_id]) || !$_ex_class->checkConstraints($object)) {
        unset($ex_classes[$_id]);
      }
      else {
        $_ex_class->_host_object = $object;
      }
    }
    
    return $ex_classes;
  }
  
  static function getFormsStruct($ex_classes) {
    $forms = array();
    
    foreach($ex_classes as $_ex_class) {
      // We may have more than one form per exclass
      $forms[] = array(
        "ex_class_id" => $_ex_class->_id,
        "object_guid" => $_ex_class->_host_object->_guid,
        "event" => $_ex_class->event,
      );
    }
    
    return array_values($forms);
  }
  
  static function getJStrigger($ex_classes) {
    if (count($ex_classes) == 0) return "";
    
    $forms = self::getFormsStruct($ex_classes);
    
    return "
    <script type='text/javascript'>
      (window.ExObject || window.opener.ExObject).triggerMulti(".json_encode($forms).");
    </script>";
  }
  
  function getGrid($w = 4, $h = 30, $reduce = true) {
    $big_grid = array();
    $big_out_of_grid = array();
    $groups = $this->loadRefsGroups(true);
    
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
        `ex_object_id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `group_id`         INT(11) UNSIGNED NOT NULL,
        
        `object_id`        INT(11) UNSIGNED NOT NULL,
        `object_class`     VARCHAR(80) NOT NULL,
        
        `reference_id`     INT(11) UNSIGNED NOT NULL,
        `reference_class`  VARCHAR(80) NOT NULL,
        
        `reference2_id`    INT(11) UNSIGNED NOT NULL,
        `reference2_class` VARCHAR(80) NOT NULL,
        
        INDEX ( `group_id` ),
        
        INDEX ( `object_id` ),
        INDEX ( `object_class` ),
        
        INDEX ( `reference_id` ),
        INDEX ( `reference_class` ),
        
        INDEX ( `reference2_id` ),
        INDEX ( `reference2_class` )
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
