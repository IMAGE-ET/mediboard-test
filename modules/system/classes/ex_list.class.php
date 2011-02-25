<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExList extends CMbObject {
  var $ex_list_id = null;
  
  var $name       = null;
  
  var $_ref_items = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list";
    $spec->key   = "ex_list_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"] = "str notNull seekable";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items"] = "CExListItem list_id";
    return $backProps;
  }
  
  function loadRefItems() {
    return $this->_ref_items = $this->loadBackRefs("items", "value");
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->name;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadBackRefs("items", "value");
  }
  
  function updateEnumSpec(CEnumSpec $spec){
    $items = $this->loadRefItems();
    $empty = empty($spec->_locales);
    
    foreach($items as $_item) {
      if (!$empty && !isset($spec->_locales[$_item->value])) continue;
      $spec->_locales[$_item->value] = $_item->name;
    }
    
    unset($spec->_locales[""]);
    $spec->_list = array_keys($spec->_locales);
    
    return $spec;
  }
}
