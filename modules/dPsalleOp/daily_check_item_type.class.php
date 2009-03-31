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
  var $title    = null;
	var $desc     = null;
  var $active   = null;
	var $group_id = null;
	
	// Refs
  var $_ref_group = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item_type';
    $spec->key   = 'daily_check_item_type_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['title']    = 'str notNull';
    $specs['desc']     = 'text';
    $specs['active']   = 'bool notNull';
    $specs['group_id'] = 'ref notNull class|CGroups';
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
    $this->_ref_group = new CGroups();
    $this->_ref_group = $this->_ref_group->getCached($this->group_id);
  }
	
  static function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
  	$item_type = new self;
		$where['group_id'] = "= '".CGroups::loadCurrent()->_id."'";
    return $item_type->loadList($where, $order, $limit, $groupby, $ljoin);
  }
}
?>