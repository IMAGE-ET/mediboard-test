<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExConcept extends CMbObject {
  var $ex_concept_id = null;
  
  var $ex_list_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  var $prop = null; 
  
  var $_ref_ex_list = null;
  var $_concept_spec = null;

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
    $props["name"]        = "str notNull";
    $props["prop"]        = "str notNull";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField concept_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->name;
    
    if ($this->ex_list_id) {
      $list = $this->loadRefExList();
      $this->_view .= " [$list->_view]";
    }
  }
  
  function loadRefExList($cache = true){
    return $this->_ref_ex_list = $this->loadFwdRef("ex_list_id", $cache);
  }
  
  function loadView(){
    parent::loadView();
    $this->_concept_spec = self::getConceptSpec($this->prop);
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
  
  /**
   * @param string $prop
   * @return CMbFieldSpec
   */
  static function getConceptSpec($prop){
    $field = "dummy";
    
    $object = new CMbObject;
    $object->$field = null;
    $object->_props[$field] = $prop;
    @$object->_specs = $object->getSpecs();
    
    $spec = @CMbFieldSpecFact::getSpec($object, $field, $prop);
    $options = $spec->getOptions();
    
    $invalid = array("moreThan", "moreEquals", "sameAs", "notContaining", "notNear", "dependsOn");
    foreach($invalid as $_invalid) {
      unset($options[$_invalid]);
    }
    
    uksort($options, array("CExConcept", "order_specs"));
    
    $spec->_options = $options;
    return $spec;
  }
}
