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

class CExClassFieldPredicate extends CMbObject {
  public $ex_class_field_predicate_id;
  
  public $ex_class_field_id;
  public $operator;
  public $value;
  public $_value;

  /**
   * @var CExClassField
   */
  public $_ref_ex_class_field;

  /**
   * @var CExClassFieldProperty
   */
  public $_ref_properties;

  static $_load_lite = false;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_predicate";
    $spec->key   = "ex_class_field_predicate_id";
    $spec->uniques["value"] = array("ex_class_field_id", "operator", "value");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_field_id"] = "ref notNull class|CExClassField cascade seekable";
    $props["operator"]          = "enum notNull list|=|!=|>|>=|<|<=|startsWith|endsWith|contains|hasValue|hasNoValue default|=";
    $props["value"]             = "str notNull seekable";
    $props["_value"]            = "str";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["display_fields"]    = "CExClassField predicate_id";
    $backProps["display_messages"]  = "CExClassMessage predicate_id";
    $backProps["display_subgroups"] = "CExClassFieldSubgroup predicate_id";
    $backProps["properties"]        = "CExClassFieldProperty predicate_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();

    if (self::$_load_lite) {
      return;
    }

    $field = $this->loadRefExClassField();
    
    $ex_object = new CExObject($field->loadRefExGroup()->ex_class_id);
    $ex_object->{$field->name} = $this->value;
    
    $this->_value = "";
    if ($this->operator != "hasValue" && $this->operator != "hasNoValue") {
      $this->_value = $ex_object->getFormattedValue($field->name);
    }
    
    $this->_view = $field->_view." ".$this->_specs["operator"]->_locales[$this->operator]." ".$this->_value;
  }
  
  /**
   * @param bool $cache [optional]
   *
   * @return CExClassField
   */
  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }
  
  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin = null, $order = null) {
    $list = $this->loadList($where, null, null, null, $ljoin);
    
    $real_list = array();
    $re = preg_quote($keywords);
    $re = CMbString::allowDiacriticsInRegexp($re);
    $re = str_replace("/", "\\/", $re);
    $re = "/($re)/i";
    
    foreach ($list as $_match) {
      if ($keywords == "%" || $keywords == "" || preg_match($re, $_match->_view)) {
        $real_list[$_match->_id] = $_match;
      }
    }
    
    $views = CMbArray::pluck($real_list, "_view");
    array_multisort($views, $real_list);
    
    $empty = new self;
    $empty->_id = null;
    $empty->_guid = "$this->_class-$this->_id"; // FIXME
    $empty->_view = " -- ";
    array_unshift($real_list, $empty);
    
    return $real_list;
  }
  
  function checkValue($value){
    return CExClass::compareValues($value, $this->operator, $this->value);
  }

  function store(){
    CExObject::$_locales_cache_enabled = false;

    return parent::store();
  }

  /**
   * @return CExClassFieldProperty[]
   */
  function loadRefProperties(){
    return $this->_ref_properties = $this->loadBackRefs("properties");
  }
}
