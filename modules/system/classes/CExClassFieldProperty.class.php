<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassFieldProperty extends CMbMetaObject {
  var $ex_class_field_property_id = null;

  var $type              = null;
  var $value             = null;
  var $_value            = null;
  var $predicate_id      = null;
  
  /**
   * @var CExClassField|CExClassMessage|CExClassFieldSubgroup
   */
  var $_ref_object = null;
  
  /**
   * @var CExClassFieldPredicate
   */
  var $_ref_predicate = null;
  
  static $_style_types = array(
    "background-color" => "color",
    "color"            => "color",
    "font-weight"      => "font",
    "font-style"       => "font",
    "font-size"        => "font",
    "font-family"      => "font",
  );

  static $_style_values = array(
    "font-weight" => array("bold",   "normal"),
    "font-style"  => array("italic", "normal"),
    "font-family" => array("sans-serif", "serif", "monospace", "cursive"),
  );

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_property";
    $spec->key   = "ex_class_field_property_id";
    $spec->uniques["type"] = array("object_class", "object_id", "type", "predicate_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["object_class"] = "enum notNull list|CExClassField|CExClassMessage|CExClassFieldSubgroup";
    $props["object_id"]    = "ref notNull class|CMbObject meta|object_class cascade";
    $props["predicate_id"] = "ref class|CExClassFieldPredicate cascade";
    $props["type"]         = "enum list|".implode("|", array_keys(self::$_style_types));
    $props["value"]        = "str";
    $props["_value"]       = "str";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_value = $this->value;
    if (array_key_exists($this->type, self::$_style_values)) {
      $this->_value = CAppUI::tr("$this->_class.value.$this->type.$this->value");
    }

    $this->_view = $this->getFormattedValue("type").": $this->value";
  }
  
  function isColor() {
    return self::$_style_types[$this->type] == "color";
  }
  
  static function getColorStyles() {
    $styles = array();
    foreach (self::$_style_types as $_key => $_type) {
      if ($_type == "color") {
        $styles[] = $_key;
      }
    }
    
    return $styles;
  }

  /**
   * @param CExClassField|CExClassMessage|CExClassFieldSubgroup $object
   *
   * @return array
   */
  static function getDefaultPropertiesFor(CMbObject $object) {
    static $types;

    if (empty($types)) {
      $prop = new self;
      $types = $prop->_specs["type"]->_list;
    }

    $default = array_fill_keys($types, ""); // Doit etre une chaine vide pour IE

    $properties = $object->loadRefProperties();
    foreach ($properties as $_property) {
      if ($_property->predicate_id || $_property->value == "") {
        continue;
      }

      $default[$_property->type] = $_property->value;
    }

    return $default;
  }
  
  /**
   * @param bool $cache
   * 
   * @return CExClassFieldPredicate
   */
  function loadRefPredicate($cache = true) {
    return $this->_ref_predicate = $this->loadFwdRef("predicate_id", $cache);
  }
}
