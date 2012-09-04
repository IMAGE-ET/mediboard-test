<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExListItem extends CMbObject {
  var $ex_list_item_id = null;
  
  var $list_id         = null;
  var $concept_id      = null;
  var $field_id        = null;
  
  var $code            = null;
  var $name            = null;
  
  var $_ref_list       = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list_item";
    $spec->key   = "ex_list_item_id";
    $spec->uniques["name"]  = array("list_id", "name");
    $spec->xor["owner"] = array("list_id", "concept_id", "field_id");
    return $spec;
  }

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
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $list = $this->loadRefList();
    $this->_view = "{$list->_view} / $this->name";
    
    if ($this->code != null) {
      $this->_view .= " [$this->code]";
    }
  }
  
  function store(){
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->fieldModified("name") || !$this->_old->_id) {
      CExObject::clearLocales();
    }
  }
}
