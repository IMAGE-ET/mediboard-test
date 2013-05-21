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

class CExListItem extends CMbObject {
  public $ex_list_item_id;
  
  public $list_id;
  public $concept_id;
  public $field_id;
  
  public $code;
  public $name;
  
  public $_ref_list;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list_item";
    $spec->key   = "ex_list_item_id";
    $spec->uniques["name"]  = array("list_id", "name");
    $spec->xor["owner"] = array("list_id", "concept_id", "field_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["list_id"]    = "ref class|CExList cascade";
    $props["concept_id"] = "ref class|CExConcept cascade";
    $props["field_id"]   = "ref class|CExClassField cascade";
    $props["code"]       = "str maxLength|20";
    $props["name"]       = "str notNull";
    return $props;
  }
  
  function loadRefList($cache = true) {
    return $this->_ref_list = $this->loadFwdRef("list_id", $cache);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $list = $this->loadRefList();
    $this->_view = "{$list->_view} / $this->name";
    
    if ($this->code != null) {
      $this->_view .= " [$this->code]";
    }
  }

  /**
   * @see parent::store()
   */
  function store(){
    $is_new = !$this->_id;
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($is_new || $this->fieldModified("name")) {
      CExObject::clearLocales();
    }

    return null;
  }
}
