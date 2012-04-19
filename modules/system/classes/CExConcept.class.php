<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExConcept extends CExListItemsOwner {
  var $ex_concept_id = null;
  
  var $ex_list_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  var $prop = null; 
  
  var $_ref_ex_list = null;
  var $_ref_class_fields = null;
  var $_concept_spec = null;
  
  static function parseSearch($concept_search) {
    $concept_search = utf8_encode($concept_search);
    $args = json_decode($concept_search);

    $search = array();
    foreach($args as $_key => $_val) {
      $matches = array();
      
      if (preg_match('/^cv(\d+)_(\d+)_([a-z]+)$/', $_key, $matches)) {
        list($p, $concept_id, $i, $k) = $matches;
        if (!isset($search[$concept_id])){
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
    $props["ex_list_id"]  = "ref class|CExList autocomplete|name";
    $props["name"]        = "str notNull seekable";
    $props["prop"]        = "str notNull show|0";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField concept_id";
    $backProps["list_items"] = "CExListItem concept_id";
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
  
  function updatePropFromList(){
    $spec = $this->loadConceptSpec();
    if (!$spec instanceof CEnumSpec) return;
    
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
    $list_re    = "/(\slist\|[^\s]+)/";
    $default_re = "/(\sdefault\|[^\s]+)/";
    
    $list_prop = "";
    $default_prop = "";
    
    $new_prop = $this->prop;
    
    // extract $prop's list|XXX
    if (preg_match($list_re, $prop, $matches)) {
      $list_prop = $matches[1];
      $new_prop = preg_replace($list_re, "", $new_prop);  
      
      // extract $prop's default|XXX
      if (preg_match($default_re, $prop, $matches)) {
        $default_prop = $matches[1];
        $new_prop = preg_replace($default_re, "", $new_prop);
      }
    }
    
    return $new_prop.$list_prop.$default_prop;
  }
  
  /**
   * @param bool $cache [optional]
   * @return CExList
   */
  function loadRefExList($cache = true){
    return $this->_ref_ex_list = $this->loadFwdRef("ex_list_id", $cache);
  }
  
  function loadRefClassFields(){
    return $this->_ref_class_fields = $this->loadBackRefs("class_fields");
  }
  
  function loadView(){
    parent::loadView();
    $this->loadConceptSpec();
    $this->loadBackRefs("class_fields");
  }
  
  function loadConceptSpec(){
    return $this->_concept_spec = self::getConceptSpec($this->prop);
  }
  
  function updateTranslation(){
    $base = $this;
    
    if ($this->concept_id) {
      $base = $this->loadRefConcept();
    }
    
    $enum_trans = $base->loadRefEnumTranslations();
    foreach($enum_trans as $_enum_trans) {
      $_enum_trans->updateLocales($this);
    }
    
    $trans = $this->loadRefTranslation();
    $this->_locale       = $trans->std;
    $this->_locale_desc  = $trans->desc;
    $this->_locale_court = $trans->court;
    
    $this->_view = $this->_locale;
    
    return $trans;
  }
    
  static function order_specs($a, $b) {
    $options_order = array(
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
    
    $key_a = array_search($a, $options_order);
    $key_b = array_search($b, $options_order);
    
    return ($key_a === false ? 1000 : $key_a) - ($key_b === false ? 1000 : $key_b);
  }
  
  function store(){
    $prop_changed = $this->fieldModified("prop");
    
    if ($msg = parent::store()){
      return $msg;
    }
    
    if ($prop_changed) {
      $fields = $this->loadRefClassFields();
      foreach($fields as $_field) {
        $new_prop = $this->updateFieldProp($_field->prop);
        $modif = ($_field->prop != $new_prop);
        
        $_field->prop = $new_prop;
        
        if ($msg = $_field->store()) {
          
        }
        else if ($modif) {
          $_field->updateTranslation();
          CAppUI::displayMsg($msg, "Champ <strong>$_field->_view</strong> mis à jour");
        }
      }
    }
  }
  
  /**
   * @param string $prop
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
    foreach($invalid as $_invalid) {
      unset($options[$_invalid]);
    }
    
    uksort($options, array("CExConcept", "order_specs"));
    
    $spec->_options = $options;
    return $spec;
  }
  
  function getRealListOwner(){
    if ($this->ex_list_id) {
      return $this->loadRefExList();
    }
    
    return parent::getRealListOwner();
  }
}
