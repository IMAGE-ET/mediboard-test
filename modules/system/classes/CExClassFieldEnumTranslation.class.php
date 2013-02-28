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

class CExClassFieldEnumTranslation extends CMbObject {
  public $ex_class_field_enum_translation_id;

  public $ex_class_field_id;
  public $lang;
  public $key;
  public $value;

  public $_ref_ex_class_field;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_enum_translation";
    $spec->key   = "ex_class_field_enum_translation_id";
    $spec->uniques["lang"] = array("ex_class_field_id", "lang", "key");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_field_id"] = "ref notNull class|CExClassField cascade";
    $props["lang"]  = "enum list|fr|en"; // @todo: en fonction des repertoires
    $props["key"]   = "str";
    $props["value"] = "str";
    return $props;
  }

  function getKey(CExClassField $base = null){
    $field = $base ? $base : $this->loadRefExClassField();
    $class = $base ? $base->loadRefExClass() : $field->loadRefExClass();
    $key = "CExObject";

    if ($class->_id) {
      $key .= "_{$class->_id}";
    }

    return "$key.{$field->name}.{$this->key}";
  }

  function updateLocales(CExClassField $base = null){
    $key = $this->getKey($base);

    global $locales;
    $locales[$key] = $this->value;
    $this->_view = $this->value;
  }

  function fillIfEmpty() {
    if (!$this->_id) {
      $this->value = $this->key;
      $this->updateLocales();
      $this->value = "";
    }
  }

  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }
}
