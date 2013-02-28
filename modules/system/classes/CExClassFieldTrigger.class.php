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

class CExClassFieldTrigger extends CMbObject {
  public $ex_class_field_trigger_id;

  public $ex_class_field_id;
  public $ex_class_triggered_id;
  public $trigger_value;

  /**
   * @var CExClassField
   */
  public $_ref_ex_class_field;

  /**
   * @var CExClassField
   */
  public $_ref_ex_class_triggered;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_trigger";
    $spec->key   = "ex_class_field_trigger_id";
    $spec->uniques["ex_class_triggered"] = array("ex_class_field_id", "trigger_value");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_field_id"]     = "ref notNull class|CExClassField cascade";
    $props["ex_class_triggered_id"] = "ref notNull class|CExClass cascade";
    $props["trigger_value"]         = "str notNull";
    return $props;
  }

  function loadView(){
    parent::loadView();
    $this->loadRefExClassField();
    $this->loadRefExClassTriggered();
    $this->_view = $this->_ref_ex_class_field->_view." > ".$this->_ref_ex_class_triggered->_view;
  }

  /**
   * @param bool $cache
   *
   * @return CExClassField
   */
  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CExClass
   */
  function loadRefExClassTriggered($cache = true){
    return $this->_ref_ex_class_triggered = $this->loadFwdRef("ex_class_triggered_id", $cache);
  }
}
