<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItem extends CMbObject {
  var $daily_check_item_id  = null;

  // DB Fields
  var $list_id      = null;
	var $item_type_id = null;
	var $checked = null;
  
  // Refs
  var $_ref_list = null;
  var $_ref_item_type = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item';
    $spec->key   = 'daily_check_item_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['list_id']      = 'ref notNull class|CDailyCheckList';
    $specs['item_type_id'] = 'ref notNull class|CDailyCheckItemType';
    $specs['checked']      = 'bool notNull';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_item_type.' ('.($this->checked != 0 ? '' : 'non').' validé)';
  }
  
  function loadRefsFwd() {
    $this->_ref_list = new CDailyCheckList();
    $this->_ref_list = $this->_ref_list->getCached($this->list_id);
		
    $this->_ref_item_type = new CDailyCheckItemType();
    $this->_ref_item_type = $this->_ref_item_type->getCached($this->item_type_id);
  }
}
?>