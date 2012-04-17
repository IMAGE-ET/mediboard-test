<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExListItemsOwner extends CMbObject {
  var $_ref_items = null;
  
  function loadRefItems() {
    $items = $this->loadBackRefs("list_items");
    self::naturalSort($items, array("code"));
    return $this->_ref_items = $items;
  }
  
  function getBackRefField(){
    $map = array(
      "CExList"       => "list_id",
      "CExConcept"    => "concept_id",
      "CExClassField" => "field_id",
    );
    return CValue::read($map, $this->_class);
  }
  
  function getItemsKeys() {
    $item = new CExListItem;
    $where = array(
      $this->getBackRefField() => "= '$this->_id'"
    );
    return $item->loadIds($where, "LPAD(code, 20, '0'), name"); // Natural sort, sort of ...
  }
  
  function getItemNames(){
    $item = new CExListItem;
    $where = array(
      $this->getBackRefField() => "= '$this->_id'"
    );
    
    $request = new CRequest();
    $request->addWhere($where);
    $request->addTable($item->_spec->table);
    $request->addOrder("LPAD(code, 20, '0'), name");
    $request->addSelect(array(
      $item->_spec->key,
      "name",
    ));

    $ds = $item->_spec->ds;
    return $ds->loadHashList($request->getRequest());
  }
  
  function getRealListOwner(){
    return $this;
  }
  
  function updateEnumSpec(CEnumSpec $spec){
    $items = $this->getItemNames();
    $empty = empty($spec->_locales);
    
    foreach($items as $_id => $_item) {
      if (!$empty && !isset($spec->_locales[$_id])) continue;
      $spec->_locales[$_id] = $_item;
    }
    
    $spec->_list = array_keys($spec->_locales);
    
    return $spec;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefItems();
  }
}
