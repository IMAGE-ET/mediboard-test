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

class CExConcept extends CExListItemsOwner {
  public $ex_concept_id;
  
  public $ex_list_id;
  public $name; // != object_class, object_id, ex_ClassName_event_id,
  public $prop;
  public $native_field;

  /** @var CExList */
  public $_ref_ex_list;

  /** @var CExClassField[] */
  public $_ref_class_fields;

  /** @var CMbFieldSpec */
  public $_concept_spec;

  public $_native_field_view;
  
  static $_options_order = array(
    "list",
    "notNull",
    "typeEnum",
    "length",
    "maxLength",
    "minLength",
    "min",
    "max",
    "pos",
    "progressive",
    
    "ccam",
    "cim10",
    "adeli",
    "insee",
    "rib",
    "siret",
    "order_number",
    
    "class",
    "cascade",
  );

  /**
   * Parse a search string, from the search form
   *
   * @param string $concept_search The keywords to search for
   *
   * @return array
   */
  static function parseSearch($concept_search) {
    $concept_search = utf8_encode($concept_search);
    $args = json_decode($concept_search);

    $search = array();
    foreach ($args as $_key => $_val) {
      $matches = array();
      
      if (preg_match('/^cv(\d+)_(\d+)_([a-z]+)$/', $_key, $matches)) {
        list(, $concept_id, $i, $k) = $matches;
        if (!isset($search[$concept_id])) {
          $search[$concept_id] = array();
        }
        
        $search[$concept_id][$i][$k] = $_val;
      }
    }
    
    return $search;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_concept";
    $spec->key   = "ex_concept_id";
    $spec->uniques["name"] = array("name", "ex_list_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_list_id"]   = "ref class|CExList autocomplete|name";
    $props["name"]         = "str notNull seekable";
    $props["prop"]         = "str notNull show|0";
    $props["native_field"] = "str show|0";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField concept_id";
    $backProps["list_items"]   = "CExListItem concept_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->name;
    
    $this->updatePropFromList();
    
    if ($this->ex_list_id) {
      $list = $this->loadRefExList();
      $this->_view .= " [$list->_view]";
    }
    else {
      $spec_type = $this->loadConceptSpec()->getSpecType();
      $this->_view .= " [".CAppUI::tr("CMbFieldSpec.type.$spec_type")."]";
    }
  }
  
  function loadEditView() {
    parent::loadEditView();

    // Vue du "native field"
    if ($this->native_field) {
      list($class, $field) = explode(" ", $this->native_field, 2);

      $ex_class_event = new CExClassEvent();
      $ex_class_event->host_class = $class;
      $list = $ex_class_event->buildHostFieldsList();

      $this->_native_field_view = "";
      if (strpos($field, "CONNECTED_USER") === false) {
        $this->_native_field_view = CAppUI::tr($class) . " / ";
      }

      $this->_native_field_view .= $list[$field]["view"];
    }
    
    $fields = $this->loadRefClassFields();
    foreach ($fields as $_field) {
      $_field->loadRefExClass();
    }
  }

  /**
   * @return void
   */
  function updatePropFromList(){
    $spec = $this->loadConceptSpec();
    if (!$spec instanceof CEnumSpec) {
      return;
    }
    
    if ($this->ex_list_id) {
      $list = $this->loadRefExList();
      $ids = $list->getItemsKeys();
    }
    else {
      $ids = $this->getItemsKeys();
    }
    
    $suffix = " list|".implode("|", $ids);
    $pattern = "/( list\|[^ ]+)/";
    
    if (!preg_match($pattern, $this->prop)) {
      $this->prop .= $suffix;
    }
    else {
      $this->prop = preg_replace($pattern, $suffix, $this->prop);
    }
  }
  
  function updateFieldProp($prop){
    //$concept_spec = $this->loadConceptSpec();
    $list_re     = "/(\slist\|[^\s]+)/";
    $default_re  = "/(\sdefault\|[^\s]+)/";
    $vertical_re = "/(\svertical(?:\|[^\s]+)?)/";
    
    $list_prop = "";
    $default_prop = "";
    $vertical_prop = "";
    
    $new_prop = preg_replace($vertical_re, "", $this->prop);
    
    // extract $prop's list|XXX
    $matches = array();
    if (preg_match($list_re, $prop, $matches)) {
      $list_prop = $matches[1];
      $new_prop = preg_replace($list_re, "", $new_prop);  
      
      // extract $prop's default|XXX
      $matches = array();
      if (preg_match($default_re, $prop, $matches)) {
        $default_prop = $matches[1];
        $new_prop = preg_replace($default_re, "", $new_prop);
      }
    }
    
    // extract $prop's vertical
    $matches = array();
    if (preg_match($vertical_re, $prop, $matches)) {
      $vertical_prop = $matches[1];
      $new_prop = preg_replace($vertical_re, "", $new_prop);  
    }
    
    return $new_prop.$list_prop.$default_prop.$vertical_prop;
  }
  
  /**
   * @param bool $cache [optional]
   *
   * @return CExList
   */
  function loadRefExList($cache = true){
    return $this->_ref_ex_list = $this->loadFwdRef("ex_list_id", $cache);
  }

  /**
   * @return CExClassField[]
   */
  function loadRefClassFields(){
    return $this->_ref_class_fields = $this->loadBackRefs("class_fields");
  }
  
  function loadView(){
    parent::loadView();
    $this->loadConceptSpec();
    $this->loadRefClassFields();
  }

  /**
   * @return CMbFieldSpec
   */
  function loadConceptSpec(){
    return $this->_concept_spec = self::getConceptSpec($this->prop);
  }

  static function getReportableFields($class = null) {
    $list = array();

    if ($class) {
      $classes = array($class);
    }
    else {
      $classes = CExClassEvent::getReportableClasses();
    }

    $full = false;

    foreach ($classes as $_class) {
      $ex_class_event = new CExClassEvent();
      $ex_class_event->host_class = $_class;
      $list = array_merge($list, $ex_class_event->buildHostFieldsList($_class));
    }

    if (!$full) {
      $select = array_flip(array(
        "CPatient _annees",
        "CPatient _poids",
        "CPatient _taille",
      ));

      $list = array_intersect_key($list, $select);
    }

    return $list;
  }
    
  static function compareSpecs($a, $b) {
    $options = self::$_options_order;
    return (isset($options[$a]) ? $options[$a] : 1000) - (isset($options[$b]) ? $options[$b] : 1000);
  }
  
  static function orderSpecs(&$options) {
    uksort($options, array("CExConcept", "compareSpecs"));
  }
  
  function store(){
    $prop_changed = $this->fieldModified("prop");
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($prop_changed) {
      $fields = $this->loadRefClassFields();
      foreach ($fields as $_field) {
        $new_prop = $this->updateFieldProp($_field->prop);
        $modif = ($_field->prop != $new_prop);
        
        $_field->prop = $new_prop;

        if ($msg = $_field->store()) {
          continue;
        }

        if ($modif) {
          $_field->updateTranslation();
          CAppUI::displayMsg($msg, "Champ <strong>$_field->_view</strong> mis à jour");
        }
      }
    }
  }
  
  /**
   * @param string $prop
   *
   * @return CMbFieldSpec
   */
  static function getConceptSpec($prop){
    if ($prop == "mbField") {
      $prop = "";
    }
    
    $field = "dummy";
    
    $object = new CMbObject;
    $object->$field = null;
    $object->_props[$field] = $prop;
    @$object->_specs = $object->getSpecs();
    
    $spec = @CMbFieldSpecFact::getSpec($object, $field, $prop);
    $options = $spec->getOptions();
    
    $invalid = array("moreThan", "moreEquals", "sameAs", "notContaining", "notNear", "dependsOn", "helped", "aidesaisie");
    foreach ($invalid as $_invalid) {
      unset($options[$_invalid]);
    }
    
    self::orderSpecs($options);
    
    $spec->_options = $options;
    return $spec;
  }

  /**
   * @return CExList|CExListItemsOwner
   */
  function getRealListOwner(){
    if ($this->ex_list_id) {
      return $this->loadRefExList();
    }
    
    return parent::getRealListOwner();
  }
}

CExConcept::$_options_order = array_flip(CExConcept::$_options_order);
