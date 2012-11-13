<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExListItemsOwner extends CMbObject {
  /**
   * @var CExListItem[]
   */
  var $_ref_items = null;

  /**
   * @var bool
   */
  static $_order_list_items = true;

  /**
   * @return CExListItem[]
   */
  function loadRefItems() {
    $items = $this->loadBackRefs("list_items");
    self::naturalSort($items, array("code"));
    return $this->_ref_items = $items;
  }

  /**
   * @return string
   */
  function getBackRefField(){
    $map = array(
      "CExList"       => "list_id",
      "CExConcept"    => "concept_id",
      "CExClassField" => "field_id",
    );
    return CValue::read($map, $this->_class);
  }

  /**
   * @return integer[]
   */
  function getItemsKeys() {
    $item = new CExListItem;
    $where = array(
      $this->getBackRefField() => "= '$this->_id'"
    );
    // TODO ne pas ordonner par nom dans certains cas

    // Natural sort, sort of ...
    $orderby = (self::$_order_list_items ? "LPAD(code, 20, '0'), name" : null);

    return $item->loadIds($where, $orderby);
  }

  /**
   * @return string[]
   */
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

  /**
   * @return CExListItemsOwner
   */
  function getRealListOwner(){
    return $this;
  }

  /**
   * @param CEnumSpec $spec
   *
   * @return CEnumSpec
   */
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
