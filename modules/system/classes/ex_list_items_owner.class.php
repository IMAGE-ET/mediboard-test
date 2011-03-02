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

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["list_items"] = "CExListItem ".$this->getBackRefField();
    return $backProps;
  }
  
  function loadRefItems() {
    return $this->_ref_items = $this->loadBackRefs("list_items", "code");
  }
	
	private function getBackRefField(){
		$map = array(
      "CExList"        => "list_id",
      "CExConcept"     => "concept_id",
      "CExClassFields" => "field_id",
    );
		return CValue::read($map, $this->_class_name);
	}
  
  function getItemsKeys() {
    $item = new CExListItem;
    $where = array(
		  $this->getBackRefField() => "= '$this->_id'"
		);
    return $item->loadIds($where, "name, code");
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefItems();
  }
}
