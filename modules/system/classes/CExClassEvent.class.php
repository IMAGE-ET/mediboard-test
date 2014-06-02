<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class event
 */
class CExClassEvent extends CMbObject {
  public $ex_class_event_id;
  
  public $ex_class_id;
  
  public $host_class;
  public $event_name;
  public $disabled;
  public $unicity;

  /** @var CExClass */
  public $_ref_ex_class;

  /** @var CExClassConstraint[] */
  public $_ref_constraints;

  /** @var CExClassFieldTrigger[] */
  public $_ref_triggers;

  /** @var CMbFieldSpec[] */
  public $_host_class_fields;

  /** @var array */
  public $_host_class_options;

  /** @var array */
  public $_available_native_views;

  /** @var CMbObject */
  public $_host_object;
  
  static $_extendable_classes = array(
    "CPrescriptionLineElement",
    "CPrescriptionLineMedicament",
    "CPrescriptionLineMixItem",
    "COperation",
    "CSejour",
    "CConsultation",
    "CConsultAnesth",
    "CAdministration",
    "CRPU",
    "CGrossesse",
    "CBilanSSR",
  );

  /**
   * Get extendable specs
   *
   * @return array
   */
  static function getExtendableSpecs(){
    $classes = self::$_extendable_classes;
    $specs = array();
    
    foreach ($classes as $_class) {
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

  /**
   * Get reportable classes
   *
   * @return string[]
   */
  static function getReportableClasses(){
    $classes = array_merge(array("CPatient", "CSejour"), self::$_extendable_classes);
    $classes[] = "CMediusers";
    return array_filter($classes, "class_exists");
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_event";
    $spec->key   = "ex_class_event_id";
    $spec->uniques["event"] = array("ex_class_id", "host_class", "event_name");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref notNull class|CExClass cascade";
    $props["host_class"]  = "str notNull protected";
    $props["event_name"]  = "str notNull protected canonical";
    $props["disabled"]    = "bool notNull default|1";
    //"enum notNull list|no|host|reference1|reference2 default|no vertical";
    $props["unicity"]     = "enum notNull list|no|host default|no vertical";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["constraints"]      = "CExClassConstraint ex_class_event_id";
    $backProps["digital_pen_copy"] = "CDigitalPenCopy ex_class_event_id";
    //$backProps["ex_triggers"]  = "CExClassFieldTrigger ex_class_event_triggered_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = CAppUI::tr($this->host_class)." - ".CAppUI::tr("$this->host_class-event-$this->event_name");
  }

  /**
   * Get available native views
   *
   * @return string[]
   */
  function getAvailableNativeViews(){
    if (!$this->_id) {
      return $this->_available_native_views = array();
    }
    
    $levels = array(1, 2, "host");
    
    $options = $this->getHostClassOptions();
    $available_views = array();

    foreach (CExClass::$_native_views as $_name => $_class) {
      foreach ($levels as $_level) {
        if ($_level == "host") {
          $ref_class = $this->host_class;
        }
        else {
          list($ref_class) = CValue::read($options, "reference$_level");
        }
        if ($_class === $ref_class) {
          $available_views[$_name] = $_class;
        }
      }
    }

    /*foreach (CExClass::$_native_views as $_name => $_classes) {
      foreach ($levels as $_level) {
        if (isset($available_views[$_name])) {
          continue;
        }

        if ($_level == "host") {
          $ref_class = $this->host_class;
        }
        else {
          list($ref_class) = CValue::read($options, "reference$_level");
        }
        if (in_array($ref_class, $_classes)) {
          $available_views[$_name] = $ref_class;
        }
      }
    }*/
    
    /*$field = "native_views";
    $this->_props[$field] = "set vertical list|".implode("|", array_keys($available_views));
    $this->_specs[$field] = CMbFieldSpecFact::getSpec($this, $field, $this->_props[$field]);
    */
    
    return $this->_available_native_views = $available_views;
  }

  /**
   * Get class options
   *
   * @return null|string[]
   */
  function getHostClassOptions(){
    if (!$this->host_class || !$this->event_name || $this->event_name === "void") {
      return null;
    }
    
    $object = new $this->host_class;
    return $this->_host_class_options = $object->_spec->events[$this->event_name];
  }

  /**
   * Load ex class
   *
   * @param bool $cache Use cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }
  
  /**
   * Returns an instance of CExObject which corresponds to the unicity
   *
   * @param CMbObject $host Host object
   *
   * @return CExObject|CExObject[]
   */
  function getExObjectForHostObject(CMbObject $host) {
    if (!$host->_id) {
      return array();
    }
    
    $this->completeField("disabled", "unicity");
    
    $existing = $this->loadRefExClass()->loadExObjects($host, $ex_object);
    $disabled = $this->disabled || !$this->checkConstraints($host);
    
    switch ($this->unicity) {
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

  /**
   * Get ExObject instance
   *
   * @param bool $cache Use cache
   *
   * @return CExObject
   */
  function getExObjectInstance($cache = false){
    return $this->loadRefExClass()->getExObjectInstance($cache);
  }

  /**
   * Resolve reference object
   *
   * @param CMbObject $object MbObject
   * @param integer   $level  Object's level (1 or 2)
   *
   * @return CMbObject|null
   */
  function resolveReferenceObject(CMbObject $object, $level = 1){
    $options = $this->getHostClassOptions();
    list($ref_class, $path) = CValue::read($options, "reference$level");
    
    if (!$object->_id) {
      return new $ref_class;
    }
    
    if (!$path) {
      return null;
    }
    
    $parts = explode(".", $path);
    
    $reference = $object;
    foreach ($parts as $_fwd) {
      $reference = $reference->loadFwdRef($_fwd, true);
    }
    
    return $reference;
  }

  /**
   * Load constraints list
   *
   * @param bool $cache Use cache
   *
   * @return CExClassConstraint[]
   */
  function loadRefsConstraints($cache = false){
    if ($cache && !empty($this->_ref_constraints)) {
      return $this->_ref_constraints;
    }

    return $this->_ref_constraints = $this->loadBackRefs("constraints");
  }

  /**
   * constraint1 OR constraint2 OR ...
   *
   * @param CMbObject $object Check constraints
   *
   * @return bool
   */
  function checkConstraints(CMbObject $object){
    $constraints = $this->loadRefsConstraints(true);
    
    if (empty($constraints)) {
      return true;
    }
    
    foreach ($constraints as $_constraint) {
      if ($_constraint->checkConstraint($object)) {
        return true;
      }
    }
    
    return false;
  }

  /**
   * Get host object's specs list
   *
   * @param CMbObject $object Object
   *
   * @return CMbFieldSpec[]
   */
  static function getHostObjectSpecs(CMbObject $object) {
    $specs = array();
    $specs["CONNECTED_USER"] = self::getConnectedUserSpec();
    return array_merge($specs, $object->_specs);
  }

  /**
   * Get "connected user" spec
   *
   * @return CMbFieldSpec
   */
  static function getConnectedUserSpec() {
    static $spec;
    
    if (!isset($spec)) {
      $mediuser = new CMediusers;
      $spec = CMbFieldSpecFact::getSpec($mediuser, "CONNECTED_USER", "ref class|CMediusers show|1");
    }
    
    return $spec;
  }

  /**
   * @see parent::check()
   */
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if ($this->fieldModified("host_class")) {
      $count_constraints = $this->countBackRefs("constraints");
      if ($count_constraints > 0) {
        return "Impossible de changer le type d'objet hôte de ce formulaire car il possède $count_constraints contrainte(s)";
      }
    }

    return null;
  }

  /**
   * Get available fields for the object
   *
   * @param CMbObject $object       Object
   * @param array     $class_fields Class fields
   *
   * @return array|CMbFieldSpec[]|null
   */
  static function getAvailableFieldsOfObject(CMbObject $object, $class_fields = null) {
    if ($class_fields === null) {
      $class_fields = $object->_specs;
    }

    foreach ($class_fields as $_field => $_spec) {
      if ($_field == $object->_spec->key) {
        unset($class_fields[$_field]);
        continue;
      }

      /*if ($_spec instanceof CRefSpec && $_spec->meta) {
        unset($class_fields[$_spec->meta]);
        continue;
      }*/

      // LEVEL 1
      if (
          // form field
          ($_field[0] === "_" && ($_spec->show === null || $_spec->show == 0)) ||
          // not shown
          !($_spec->show === null || $_spec->show == 1) ||
          // not a finite meta class field
          $_spec instanceof CRefSpec && $_spec->meta && !$class_fields[$_spec->meta] instanceof CEnumSpec
      ) {
        unset($class_fields[$_field]);
        continue;
      }

      // LEVEL 2
      if ($_spec instanceof CRefSpec) {
        // LEVEL 2 + Class list
        if ($_spec->meta && $class_fields[$_spec->meta] instanceof CEnumSpec) {
          unset($class_fields[$_field]);

          // boucle sur les classes du enum
          $classes = $class_fields[$_spec->meta]->_list;

          foreach ($classes as $_class) {
            $_key = "$_field.$_class";

            $_target = new $_class;

            $class_fields[$_key] = new CRefSpec($object->_class, $_field, "ref class|$_class");
            $class_fields[$_key]->_subspecs = array();

            foreach ($_target->_specs as $_subfield => $_subspec) {
              if (!$_subfield || $_subfield === $_target->_spec->key) {
                continue;
              }

              if (
                  // form field
                  $_subfield[0] === "_" && ($_subspec->show === null || $_subspec->show == 0) ||
                  // not shown
                  !($_subspec->show === null || $_subspec->show == 1) ||
                  // not a finite meta class field
                  $_subspec instanceof CRefSpec && $_subspec->meta && !$_target->_specs[$_subspec->meta] instanceof CEnumSpec
              ) {
                continue;
              }

              $class_fields[$_key]->_subspecs[$_subfield] = $_subspec;
            }
          }
        }
        else {
          // LEVEL 2 + Single class
          $_key = $_field;
          $class_fields[$_key]->_subspecs = array();

          $_class = $_spec->class;
          if (!$_class) {
            continue;
          }

          $_target = new $_class;

          foreach ($_target->_specs as $_subfield => $_subspec) {
            if (!$_subfield || $_subfield === $_target->_spec->key) {
              continue;
            }

            if (
                // form field
                $_subfield[0] === "_" ||
                // not shown
                !($_subspec->show === null || $_subspec->show == 1) ||
                // not a finite meta class field
                $_subspec instanceof CRefSpec && $_subspec->meta &&
                isset($object->_specs[$_subspec->meta]) &&
                !$object->_specs[$_subspec->meta] instanceof CEnumSpec
            ) {
              continue;
            }

            $class_fields[$_key]->_subspecs[$_subfield] = $_subspec;
          }
        }
      }
    }

    return $class_fields;
  }

  /**
   * Get available fields
   *
   * @return CMbFieldSpec[]
   */
  function getAvailableFields(){
    $object = new $this->host_class;

    $class_fields = self::getHostObjectSpecs($object);
    $class_fields = self::getAvailableFieldsOfObject($object, $class_fields);
    
    return $this->_host_class_fields = $class_fields;
  }

  /**
   * Build host fields list
   *
   * @param string $prefix Prefix, to restrain list
   *
   * @return array
   */
  function buildHostFieldsList($prefix = null) {
    $this->getAvailableFields();

    $list = array();
    foreach ($this->_host_class_fields as $_field => $_spec) {
      $host_class = $this->host_class;

      if ("CONNECTED_USER" === $_field) {
        $host_class = "CMediusers";
      }

      $element = array(
        "prop"  => $_spec,
        "title" => null,
        "view"  => null,
        "longview"  => null,
        "type"  => null,
        "level" => 0,
        "field" => null,
        "class" => $host_class,
      );
      
      $_subfield = explode(".", $_field);
      
      // Level 1 title
      if ($_spec instanceof CRefSpec && $_spec->class) {
        if ($_spec->meta) {
          /** @var CEnumSpec $_meta_spec */
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
      
      $list[($prefix ? "$prefix " : "") . $_field] = $element;
      
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
            "class" => $host_class,
          );
          
          if ($_subspec instanceof CRefSpec && $_subspec->class) {
            if (!$_subspec->meta) {
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
          
          $list[($prefix ? "$prefix " : "") . "$_field-$_key"] = $element;
        }
      }
    }
    
    return $list;
  }

  /**
   * Check if we can create a new instance of the ExObject
   *
   * @param CMbObject $host Host object
   *
   * @return bool
   */
  function canCreateNew(CMbObject $host) {
    switch ($this->unicity) {
      default:
      case "no":
        return true;
        
      case "host":
        // Host
        $ex_object = new CExObject($this->_id);
        $ex_object->setObject($host);
        
        if ($ex_object->countMatchingList() > 0) {
          return false;
        }
        /*
        // Reférence 1
        $ex_object = new CExObject($this->_id);
        $ex_object->setReferenceObject_1($host);
        
        if ($ex_object->countMatchingList() > 0) {
          return false;
        }
        
        // Référence 2
        $ex_object = new CExObject($this->_id);
        $ex_object->setReferenceObject_2($host);
        
        if ($ex_object->countMatchingList() > 0) {
          return false;
        }*/
    }
    
    return true;
  }

  /**
   * Build JS code for the trigger
   *
   * @param CExClassEvent[] $ex_class_events List of events
   *
   * @return string
   */
  static function getJStrigger($ex_class_events) {
    if (count($ex_class_events) == 0) {
      return "";
    }
    
    $forms = self::getFormsStruct($ex_class_events);
    
    return "
    <script type='text/javascript'>
      (window.ExObject || window.opener.ExObject).triggerMulti(".json_encode($forms).");
    </script>";
  }

  /**
   * Get forms structure, to be used in self::getJStrigger
   *
   * @param CExClassEvent[] $ex_class_events List of events
   *
   * @return array
   */
  static function getFormsStruct($ex_class_events) {
    $forms = array();
    
    foreach ($ex_class_events as $_ex_class_event) {
      // We may have more than one form per exclass
      $forms[] = array(
        "ex_class_event_id" => $_ex_class_event->_id,
        "ex_class_id"       => $_ex_class_event->ex_class_id,
        "object_guid"       => $_ex_class_event->_host_object->_guid,
        "event_name"        => $_ex_class_event->event_name,
      );
    }
    
    return array_values($forms);
  }

  /**
   * Get events for an object
   *
   * @param CMbObject|string $object                     Object or GUID
   * @param string           $event_name                 Event name
   * @param string           $type                       Type: required, disabled or conditional
   * @param array            $exclude_ex_class_event_ids List of class events' ids
   *
   * @return CExClassEvent[]
   */
  static function getForObject($object, $event_name, $type = "required", $exclude_ex_class_event_ids = array()) {
    static $events_cache = array();

    if (is_string($object)) {
      $object = CMbObject::loadFromGuid($object);
    }
    
    if ($type == "required" && !CValue::read($object->_spec->events[$event_name], "auto", false)) {
      return array();
    }
    
    $ex_class_event = new self;
    
    $group_id = CGroups::loadCurrent()->_id;
    $ds = $ex_class_event->_spec->ds;

    $key = "$object->_class/$event_name/$group_id/$type";

    if (isset($events_cache[$key])) {
      $ex_class_events = $events_cache[$key];
    }
    else {
      $where = array(
        "ex_class_event.host_class" => $ds->prepare("=%", $object->_class),
        "ex_class_event.event_name" => $ds->prepare("=%", $event_name),
        "ex_class_event.disabled"   => $ds->prepare("=%", 0),
        "ex_class.conditional"      => $ds->prepare("=%", 0),
        $ds->prepare("ex_class.group_id = % OR group_id IS NULL", $group_id),
      );
      $ljoin = array(
        "ex_class" => "ex_class.ex_class_id = ex_class_event.ex_class_id"
      );

      switch ($type) {
        case "disabled":
          $where["ex_class_event.disabled"] = 1;
          break;
        case "conditional":
          $where["ex_class.conditional"] = 1;
          break;
      }

      /** @var CExClassEvent[] $ex_class_events */
      $ex_class_events = $ex_class_event->loadList($where, null, null, null, $ljoin);

      $events_cache[$key] = $ex_class_events;
    }
    
    foreach ($ex_class_events as $_id => $_ex_class_event) {
      if (isset($exclude_ex_class_event_ids[$_id]) || !$_ex_class_event->checkConstraints($object)) {
        unset($ex_class_events[$_id]);
      }
      else {
        $_ex_class_event->_host_object = $object;
      }
    }
    
    return $ex_class_events;
  }
}
