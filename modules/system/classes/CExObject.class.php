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
 * Form data
 */
class CExObject extends CMbMetaObject {
  public $ex_object_id;
  
  public $group_id;
  
  public $reference_class;
  public $reference_id;
  
  public $reference2_class;
  public $reference2_id;
  
  public $_ex_class_id;
  public $_own_ex_class_id;
  public $_specs_already_set = false;
  public $_native_views;
  public $_event_name;
  
  /** @var CExClass */
  public $_ref_ex_class;
  
  /** @var CMbObject */
  public $_ref_reference_object_1;
  
  /** @var CMbObject */
  public $_ref_reference_object_2;
  
  /** @var CGroups */
  public $_ref_group;
  
  public $_reported_fields = array();
  public $_fields_display = array();
  public $_fields_display_struct = array();
  public $_fields_default_properties = array();
  public $_formula_result;

  static $_load_lite      = false;
  static $_multiple_load  = false;
  
  static $_ex_specs       = array();
  
  static $_locales_ready = false;
  static $_locales_cache_enabled = true;

  /**
   * Custom constructor
   *
   * @param ref $ex_class_id CExClass id
   */
  function __construct($ex_class_id = null){
    parent::__construct();
  
    if (self::$_multiple_load) {
      $class = get_class($this);
      unset(self::$spec[$class]);
      unset(self::$props[$class]);
      unset(self::$specs[$class]);
      unset(self::$backProps[$class]);
      unset(self::$backSpecs[$class]);
    }
    
    if ($ex_class_id) {
      $this->setExClass($ex_class_id);
    }
  }

  /**
   * Sets the CExClass ID of $this
   *
   * @param ref $ex_class_id CExClass ID
   *
   * @return void
   */
  function setExClass($ex_class_id = null) {
    if ($ex_class_id) {
      $this->_ex_class_id = $ex_class_id;
    }
    
    if ($this->_specs_already_set || !$this->_ex_class_id && !$this->_own_ex_class_id) {
      return;
    }
    
    if (CExObject::$_locales_cache_enabled) {
      self::initLocales();
    }
    
    $this->_props = $this->getProps();
    
    CBoolSpec::$_default_no = false;
    $this->_specs = @$this->getSpecs(); // when creating the field
    CBoolSpec::$_default_no = true;
    
    $ex_class = $this->_ref_ex_class;
    
    $this->_class = "CExObject_{$ex_class->_id}";
    
    $this->_own_ex_class_id = $ex_class->_id;
    $this->_ref_ex_class = $ex_class;
    
    $this->_specs_already_set = true;
  }

  /**
   * Load Ex class
   *
   * @param bool $cache Use object cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true){
    if ($cache && $this->_ref_ex_class && $this->_ref_ex_class->_id) {
      return $this->_ref_ex_class;
    }
    
    $id = $this->getClassId();
    if (isset(CExClass::$_list_cache[$id])) {
      return $this->_ref_ex_class = CExClass::$_list_cache[$id];
    }
    
    $ex_class = new CExClass();
    $ex_class->load($this->getClassId());
    
    return $this->_ref_ex_class = $ex_class; // can't use loadFwdRef here
  }

  /**
   * Clears locales cache
   *
   * @return void
   */
  static function clearLocales() {
    $languages = CAppUI::getAvailableLanguages();

    foreach ($languages as $_lang) {
      SHM::rem("exclass-locales-$_lang");
    }

    self::$_locales_ready = false;
  }

  /**
   * Inits locale cache
   *
   * @return void
   */
  static function initLocales(){
    if (self::$_locales_ready) {
      return;
    }
    
    $lang = CAppUI::pref("LOCALE");

    $_locales = array();

    $request = new CRequest();
    $request->addTable("ex_class_field_translation");
    $request->addWhere(array(
      "lang" => "= '$lang'",
    ));
    $request->addLJoin(array(
      "ex_class_field"       => "ex_class_field.ex_class_field_id = ex_class_field_translation.ex_class_field_id",
      "ex_concept"           => "ex_concept.ex_concept_id = ex_class_field.concept_id",
      "ex_class_field_group" => "ex_class_field_group.ex_class_field_group_id = ex_class_field.ex_group_id",
    ));
    $request->addSelect(array(
      "ex_class_field_translation.std",
      "IF(ex_class_field_translation.desc, ex_class_field_translation.desc, ex_class_field_translation.std) AS `desc`",
      "IF(ex_class_field_translation.court, ex_class_field_translation.court, ex_class_field_translation.std) AS `court`",
      "ex_class_field.ex_class_field_id AS field_id",
      "ex_class_field.name",
      "ex_class_field.prop",
      "ex_class_field.concept_id",
      "ex_class_field_group.ex_class_id",
      "ex_concept.ex_list_id",
    ));

    $ds = CSQLDataSource::get("std");
    $list = $ds->loadList($request->getRequest());

    // Chargement des list_items par concept, field ou list
    $request = new CRequest();
    $request->addTable("ex_list_item");
    $request->addSelect(array(
      "ex_list_item_id",

      "list_id",
      "concept_id",
      "field_id",

      "name",
    ));
    $list_items = $ds->loadList($request->getRequest());

    // Chargement en une seule requete de toutes les traductions de champs
    $enum_list_cache = array(
      "list"    => array(),
      "concept" => array(),
      "field"   => array(),
    );
    $mapper = array(
      "list_id"    => "list",
      "concept_id" => "concept",
      "field_id"   => "field",
    );
    foreach ($list_items as $_item) {
      $_item_id = $_item["ex_list_item_id"];
      $_item_name = $_item["name"];

      foreach ($mapper as $_field_name => $_to) {
        if ($_field_value = $_item[$_field_name]) {
          $enum_list_cache[$_to][$_field_value][$_item_id] = $_item_name;
        }
      }
    }

    foreach ($list as $_item) {
      $key = "CExObject_{$_item['ex_class_id']}-{$_item['name']}";
      $_locales[$key]         = $_item["std"];
      $_locales["$key-desc"]  = $_item["desc"];
      $_locales["$key-court"] = $_item["court"];

      $prop = $_item["prop"];
      if (strpos($prop, "enum") === false && strpos($prop, "set") === false) {
        continue;
      }

      $key = "CExObject_{$_item['ex_class_id']}.{$_item['name']}";
      $_locales["$key."] = CAppUI::tr("Undefined");

      $concept_id = $_item["concept_id"];
      $ex_list_id = $_item["ex_list_id"];
      $field_id   = $_item["field_id"];

      $enum_list = array();
      if ($concept_id) {
        if ($ex_list_id) {
          if (isset($enum_list_cache["list"][$ex_list_id])) {
            $enum_list = $enum_list_cache["list"][$ex_list_id];
          }
        }
        else {
          if (isset($enum_list_cache["concept"][$concept_id])) {
            $enum_list = $enum_list_cache["concept"][$concept_id];
          }
        }
      }
      else {
        if (isset($enum_list_cache["field"][$field_id])) {
          $enum_list = $enum_list_cache["field"][$field_id];
        }
      }

      foreach ($enum_list as $_value => $_locale) {
        $_locales["$key.$_value"] = $_locale;
      }
    }

    global $locales;
    $locales = array_merge($locales, $_locales);
    
    self::$_locales_ready = true;
  }
  
  function setReferenceObject_1(CMbObject $reference) {
    $this->_ref_reference_object_1 = $reference;
    $this->reference_class = $reference->_class;
    $this->reference_id = $reference->_id;
  }
  
  function setReferenceObject_2(CMbObject $reference) {
    $this->_ref_reference_object_2 = $reference;
    $this->reference2_class = $reference->_class;
    $this->reference2_id = $reference->_id;
  }
  
  function loadRefReferenceObjects(){
    $this->_ref_reference_object_1 = $this->loadFwdRef("reference_id");
    $this->_ref_reference_object_2 = $this->loadFwdRef("reference2_id");
  }

  /**
   * @param bool $cache
   *
   * @return CGroups
   */
  function loadRefGroup($cache = true){
    return $this->_ref_group = $this->loadFwdRef("group_id", $cache);
  }
  
  function loadNativeViews(CExClassEvent $event){
    $this->_native_views = array();
    
    $views = $event->getAvailableNativeViews();
    $selected_views = explode('|', $this->_ref_ex_class->native_views);
    
    foreach ($views as $_name => $_class) {
      if (in_array($_name, $selected_views)) {
        $this->_native_views[$_name] = $this->getReferenceObject($_class);
      }
    }
    
    return $this->_native_views;
  }
  
  /**
   * Permet de supprimer les valeurs non presentes dans les 
   * specs du champ dans ce formulaire, mais qui le sont peut
   * etre dans le meme champ dans un autre formulaire (cas d'un concept)
   *
   * @return string
   */
  static function typeSetSpecIntersect($field, $value) {
    $field_spec = $field->getSpecObject();
    
    if (!$field_spec instanceof CSetSpec) {
      return $value;
    }
    
    $values = explode("|", $value);
    $values = array_intersect($values, $field_spec->_list);
    
    return implode("|", $values);
  }

  /*
   * attention aux dates, il faut surement checker le log de derniere modif des champs du concept
   *
   * @fixme pas trop optimisé
   */
  function getReportedValues(CExClassEvent $event){
    if ($this->_id) {
      return;
    }

    self::$_multiple_load = true;
    CExClassField::$_load_lite = true;

    $this->loadTargetObject();
    $this->loadRefReferenceObjects();

    $ex_class = $this->_ref_ex_class;
    $latest_ex_objects = array(
      $ex_class->getLatestExObject($this->_ref_object),
      $ex_class->getLatestExObject($this->_ref_reference_object_1),
      $ex_class->getLatestExObject($this->_ref_reference_object_2),
    );

    if ($this->_ref_object->_id) {
      $this->_ref_object->loadComplete();
    }

    if ($this->_ref_reference_object_1->_id) {
      $this->_ref_reference_object_1->loadComplete();
    }

    if ($this->_ref_reference_object_2->_id) {
      $this->_ref_reference_object_2->loadComplete();
    }
    
    $fields = $ex_class->loadRefsAllFields(true);
    
    // Cache de concepts
    $concepts = array();
    $ex_classes = array();
    
    // on cherche les champs reportés de l'objet courant
    foreach ($fields as $_field) {
      $field_name = $_field->name;
      $this->_reported_fields[$field_name] = null;
      
      // valeur par défaut
      $spec_obj = $_field->getSpecObject();
      $this->$field_name = CExClassField::unescapeProp($spec_obj->default);

      $_concept = null;
      if ($_field->concept_id) {
        $_concept = $_field->loadRefConcept();
      }

      // si champ pas reporté, on passe au suivant
      if (!($_field->report_class || $_field->concept_id && $_concept->native_field)) {
        continue;
      }

      // Native fields
      if ($_concept && $_concept->native_field) {
        list($_class, $_path) = explode(" ", $_concept->native_field, 2);

        if (isset($this->_preview)) {
          $this->_reported_fields[$field_name] = new $_class();
          $this->$field_name = "Test";
        }
        else {
          if ($this->_ref_object->_class == $_class) {
            $_object = $this->_ref_object;
          }
          elseif ($this->_ref_reference_object_1->_class == $_class) {
            $_object = $this->_ref_reference_object_1;
          }
          elseif ($this->_ref_reference_object_2->_class == $_class) {
            $_object = $this->_ref_reference_object_2;
          }

          list($_object, $_path) = CExClassConstraint::getFieldAndObjectStatic($_object, $_path);
          $_resolved = CExClassConstraint::resolveObjectFieldStatic($_object, $_path);

          $_obj        = $_resolved["object"];
          $_field_name = $_resolved["field"];

          $this->_reported_fields[$field_name] = $_object;
          $this->$field_name = $_obj->$_field_name;
        }

        if ($this->$field_name) {
          continue;
        }
      }
      
      $_report_class = $_field->report_class;
      
      // si champ basé sur un concept, il faut parcourir 
      // tous les formulaires qui ont un champ du meme concept
      
      if ($_field->concept_id) {
        if (!isset($concepts[$_field->concept_id])) {
          $_concept_fields = $_concept->loadRefClassFields();
          
          foreach ($_concept_fields as $_concept_field) {
            if (!isset($ex_classes[$_concept_field->ex_group_id])) {
              $ex_classes[$_concept_field->ex_group_id] = $_concept_field->loadRefExClass();
            }
            else {
              $_concept_field->_ref_ex_class = $ex_classes[$_concept_field->ex_group_id];
            }
          }
          
          $concepts[$_field->concept_id] = array(
            $_concept,
            $_concept_fields,
          );
        }
        else {
          list(, $_concept_fields) = $concepts[$_field->concept_id];
        }
        
        $_latest = null;
        $_latest_value = null;
        
        // on regarde tous les champs du concept
        foreach ($_concept_fields as $_concept_field) {
          $_ex_class = $_concept_field->_ref_ex_class;

          $_concept_latest = null;
          
          if ($this->_ref_object->_class == $_report_class) {
            $_concept_latest = $_ex_class->getLatestExObject($this->_ref_object);
          }
          elseif ($this->_ref_reference_object_1->_class == $_report_class) {
            $_concept_latest = $_ex_class->getLatestExObject($this->_ref_reference_object_1);
          }
          elseif ($this->_ref_reference_object_2->_class == $_report_class) {
            $_concept_latest = $_ex_class->getLatestExObject($this->_ref_reference_object_2);
          }
          
          // si pas d'objet precedemment enregistré
          if (!$_concept_latest || !$_concept_latest->_id || $_concept_latest->{$_concept_field->name} == "") {
            continue;
          }
          
          // on regarde le log pour voir lequel a été saisi en dernier
          //$_log = $_concept_latest->loadLastLogForField($_concept_field->name); // FIXME ne donne rien quand type=create 
          $_log = $_concept_latest->loadLastLog();
          
          if (!$_latest) {
            $_latest = $_concept_latest;
            $_latest_value = $_latest->{$_concept_field->name};
          }
          else {
            if ($_log->date > $_latest->_ref_last_log->date) {
              $_latest = $_concept_latest;
              $_latest_value = $_latest->{$_concept_field->name};
            }
          }
        }
        
        if ($_latest) {
          $_latest->loadTargetObject()->loadComplete();
          
          $this->_reported_fields[$field_name] = $_latest;
          $this->$field_name = self::typeSetSpecIntersect($_field, $_latest_value);
        }
      }

      // Ceux de la meme exclass
      else {
        $escape = true;
        foreach ($latest_ex_objects as $_latest_ex_object) {
          if ($_latest_ex_object->_id) {
            $escape = false;
            break;
          }
        }
        
        if ($escape) {
          continue;
        }

        /** @var CMbObject $_base */
        $_base = null;
        foreach ($latest_ex_objects as $_latest_ex_object) {
          if ($_latest_ex_object->_ref_reference_object_1->_class == $_report_class) {
            $_base = $_latest_ex_object->_ref_reference_object_1;
            break;
          }
          elseif ($_latest_ex_object->_ref_reference_object_2->_class == $_report_class) {
            $_base = $_latest_ex_object->_ref_reference_object_2;
            break;
          }
          elseif ($_latest_ex_object->_ref_object->_class == $_report_class) {
            $_base = $_latest_ex_object->_ref_object;
            break;
          }
        }

        if ($this->_ref_object->_id && !$_base) {
          //$_field_view = CAppUI::tr("$this->_class-$_field->name");
          //CAppUI::setMsg("Report de données impossible pour le champ '$_field_view'", UI_MSG_WARNING);
          continue;
        }

        if ($_base->$field_name == "") {
          continue;
        }
        
        $_base->loadTargetObject()->loadComplete();
        $_base->loadLastLog();
        
        $this->_reported_fields[$field_name] = $_base;
        $this->$field_name = self::typeSetSpecIntersect($_field, $_base->$field_name);
      }
    }
    
    self::$_multiple_load = false;
    CExClassField::$_load_lite = false;
  }
  
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new self;
      $this->_old->_ex_class_id = $this->_ex_class_id;
      $this->_old->setExClass();
      $this->_old->load($this->_id);
    }
    
    return $this->_old; 
  }

  private function camelize($str) {
    return preg_replace("/-+(.)?/e", "ucwords('\\1')", $str);
  }

  function setFieldsDisplay(){
    $fields = $this->_ref_ex_class->loadRefsAllFields(true);

    $default = array();
    $this->_fields_display_struct = array();
    
    foreach ($fields as $_field) {
      $_affected = array();
      $_predicates = $_field->loadRefPredicates();

      foreach ($_predicates as $_predicate) {
        $_struct = array(
          "operator" => $_predicate->operator,
          "value"    => $_predicate->value,
          "display"  => array(
            "fields"    => array(),
            "messages"  => array(),
            "subgroups" => array(),
          ),
          "style"    => array(
            "fields"    => array(),
            "messages"  => array(),
            "subgroups" => array(),
          ),
        );

        // Fields
        $_display_fields = $_predicate->loadBackRefs("display_fields");
        foreach ($_display_fields as $_display) {
          $_struct["display"]["fields"][] = $_display->name;
        }

        // Messages
        $_display_messages = $_predicate->loadBackRefs("display_messages");
        foreach ($_display_messages as $_display) {
          $_struct["display"]["messages"][] = $_display->_guid;
        }

        // Subgroups
        $_display_subgroups = $_predicate->loadBackRefs("display_subgroups");
        foreach ($_display_subgroups as $_display) {
          $_struct["display"]["subgroups"][] = $_display->_guid;
        }

        $_styles = $_predicate->loadRefProperties();
        foreach ($_styles as $_style) {
          /** @var CExClassField|CExClassMessage|CExClassFieldSubgroup $_ref_object */
          $_ref_object = $_style->loadTargetObject();

          $default[$_ref_object->_guid] = $_ref_object->getDefaultProperties();

          switch ($_style->object_class) {
            case "CExClassField":
              $_field_name = $_ref_object->name;
              $_struct["style"]["fields"][] = array(
                "name"      => $_field_name,
                "type"      => $_style->type,
                "camelized" => $this->camelize($_style->type),
                "value"     => $_style->value,
              );

              $_affected[$_ref_object->_guid] = array(
                "type" => "field",
                "name" => $_field_name,
              );
              break;

            case "CExClassMessage":
              $_struct["style"]["messages"][] = array(
                "guid"      => $_ref_object->_guid,
                "type"      => $_style->type,
                "camelized" => $this->camelize($_style->type),
                "value"     => $_style->value,
              );

              $_affected[$_ref_object->_guid] = array(
                "type" => "message",
                "guid" => $_ref_object->_guid,
              );
              break;

            case "CExClassFieldSubgroup":
              $_struct["style"]["subgroups"][] = array(
                "guid"      => $_ref_object->_guid,
                "type"      => $_style->type,
                "camelized" => $this->camelize($_style->type),
                "value"     => $_style->value,
              );

              $_affected[$_ref_object->_guid] = array(
                "type" => "subgroup",
                "guid" => $_ref_object->_guid,
              );
              break;
          }
        }

        $this->_fields_display_struct[$_field->name]["predicates"][] = $_struct;
      }

      if (!empty($_affected)) {
        $this->_fields_display_struct[$_field->name]["affects"] = $_affected;
      }
    }

    $this->_fields_default_properties = $default;
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($msg = $this->check()) {
      return $msg;
    }
    
    if (!$this->_id && !$this->group_id) {
      $this->group_id = CGroups::loadCurrent()->_id;
    }
    
    return parent::store();
  }
  
  /// Low level methods ///////////
  function bind($hash, $doStripSlashes = true) {
    $this->setExClass();
    return parent::bind($hash, $doStripSlashes);
  }
  
  function load($id = null) {
    $this->setExClass();
    return parent::load($id);
  }
  
  // Used in updatePlainFields
  function getPlainFields() {
    $this->setExClass();
    return parent::getPlainFields();
  }
  
  function fieldModified($field, $value = null) {
    $this->setExClass();
    return parent::fieldModified($field, $value);
  }

  function loadQueryList($sql) {
    $ds = $this->_spec->ds;
    $cur = $ds->exec($sql);
    $list = array();

    while ($row = $ds->fetchAssoc($cur)) {
      $newObject = new self; // $this->_class >>>> "self"
      //$newObject->_ex_class_id = $this->_ex_class_id;
      //$newObject->setExClass();
      $newObject->bind($row, false);
      
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $newObject->registerCache();
      $list[$newObject->_id] = $newObject;
    }

    $ds->freeResult($cur);
    return $list;
  }
  
  // needed or will throw errors in the field specs
  function checkProperty($propName) {
    $class = $this->_class;
    $this->_class = get_class($this);
    
    $spec = $this->_specs[$propName];
    $ret = $spec->checkPropertyValue($this);
    
    $this->_class = $class;
    return $ret;
  }
  /// End low level methods /////////

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key = "ex_object_id";
    return $spec;
  }
  
  function getClassId(){
    return ($this->_ex_class_id ? $this->_ex_class_id : $this->_own_ex_class_id);
  }
  
  function getTableName(){
    return "ex_object_".$this->getClassId();
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $this->loadRefExClass();
    $this->_spec->table = $this->getTableName();
    
    $class = get_class($this)."_".$this->getClassId();
    $props = parent::getProps();
    $props["ex_object_id"]     = "ref class|$class show|0";
    $props["_ex_class_id"]     = "ref class|CExClass";
    $props["_event_name"]      = "str";
    
    $props["group_id"]         = "ref class|CGroups notNull";
    
    $props["reference_class"]  = "str class";
    $props["reference_id"]     = "ref class|CMbObject meta|reference_class";
    
    $props["reference2_class"] = "str class";
    $props["reference2_id"]    = "ref class|CMbObject meta|reference2_class";
    
    if (self::$_load_lite) {
      return $props;
    }
    
    $fields = $this->_ref_ex_class->loadRefsAllFields(true);
    
    foreach ($fields as $_field) {
      // don't redeclare them more than once
      if (isset($this->{$_field->name})) {
        break; 
      }
      
      $this->{$_field->name} = null; // declaration of the field
      $props[$_field->name] = $_field->prop; // declaration of the field spec
      $this->_fields_display[$_field->name] = true; // display the field by default
    }
    
    return $props;
  }

  /**
   * @see parent::getSpecs()
   */
  function getSpecs(){
    $ex_class_id = $this->getClassId();
    $this->_class = get_class($this)."_$ex_class_id";
    
    if ($this->_id && isset(self::$_ex_specs[$ex_class_id])) {
      return self::$_ex_specs[$ex_class_id];
    }
    
    $specs = @parent::getSpecs(); // sometimes there is "list|"
        
    foreach ($specs as $_field => $_spec) {
      if ($_spec instanceof CEnumSpec) {
        foreach ($_spec->_locales as $key => $locale) {
          $specs[$_field]->_locales[$key] = CAppUI::tr("$this->_class.$_field.$key");
        }
      }
    }
    
    return self::$_ex_specs[$ex_class_id] = $specs;
  }
  
  function loadLogs(){
    $this->setExClass();
    $ds = $this->_spec->ds;
    
    $where = array(
      "object_class" => $ds->prepare("=%", $this->_class),
      "object_id"    => $ds->prepare("=%", $this->_id)
    );
    
    $log = new CUserLog();
    $this->_ref_logs = $log->loadList($where, "user_log_id DESC", 100);

    // loadRefsFwd will fail because the ExObject's class doesn't really exist
    foreach ($this->_ref_logs as &$_log) {
      $_log->loadRefUser();
    }
    
    // the first is at the end because of the date order !
    $this->_ref_first_log = end($this->_ref_logs);
    $this->_ref_last_log  = reset($this->_ref_logs);
  }
  
  function getReferenceObject($class) {
    $fields = array(
      "object_class"     => "object_id", 
      "reference_class"  => "reference_id", 
      "reference2_class" => "reference2_id", 
    );
    
    foreach ($fields as $_class => $_id) {
      if ($this->$_class == $class) {
        return $this->loadFwdRef($_id);
      }
    }
  }
  
  static function getValidObject($object_class) {
    if (!preg_match('/^CExObject_(\d+)$/', $object_class, $matches)) {
      return false;
    }
    
    $ex_class = new CExClass();
    if (!$ex_class->load($matches[1])) {
      return false;
    }
    
    return new CExObject($ex_class->_id);
  }
}
