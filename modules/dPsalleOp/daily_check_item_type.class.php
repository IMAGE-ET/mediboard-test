<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItemType extends CMbObject {
  var $daily_check_item_type_id  = null;

  // DB Fields
  var $title       = null;
  var $desc        = null;
  var $active      = null;
  var $attribute   = null;
  var $group_id    = null;
  var $category_id = null;
	
  var $_checked    = null;
  var $_answer     = null;
  
  // Refs
  var $_ref_group  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item_type';
    $spec->key   = 'daily_check_item_type_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['title']       = 'str notNull';
    $specs['desc']        = 'text';
    $specs['active']      = 'bool notNull';
    $specs['attribute']   = 'enum list|normal|notrecommended|notapplicable default|normal';
    $specs['group_id']    = 'ref class|CGroups';
    $specs['category_id'] = 'ref notNull class|CDailyCheckItemCategory autocomplete|title';
    return $specs;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['items'] = 'CDailyCheckItem item_type_id';
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->title;
    if ($this->active == 0) {
      $this->_view = ' (Désactivé)';
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_group = $this->loadFwdRef("group_id", true);
    $this->_ref_category = $this->loadFwdRef("category_id", true);
  }
	
  static function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
  	$item_type = new self;
    $where['group_id'] = "= '".CGroups::loadCurrent()->_id."' OR group_id IS NULL";
    return $item_type->loadList($where, $order, $limit, $groupby, $ljoin);
  }
}
?>