<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItemType extends CMbObject {
  public $daily_check_item_type_id;

  // DB Fields
  public $title;
  public $desc;
  public $active;
  public $attribute;
  public $group_id;
  public $category_id;
  public $default_value;
  public $index;

  public $_checked;
  public $_answer;

  /** @var CGroups */
  public $_ref_group;

  /** @var CDailyCheckItemCategory */
  public $_ref_category;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item_type';
    $spec->key   = 'daily_check_item_type_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['title']       = 'str notNull';
    $props['desc']        = 'text';
    $props['active']      = 'bool notNull';
    $props['attribute']   = 'enum list|normal|notrecommended|notapplicable default|normal';
    $props['group_id']    = 'ref class|CGroups';
    $props['category_id'] = 'ref notNull class|CDailyCheckItemCategory autocomplete|title';
    $props['default_value'] = 'enum notNull list|yes|no|nr|na default|yes';
    $props['index']       = 'num notNull min|1';
    return $props;
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
    $this->loadRefGroup();
    $this->loadRefCategory();
  }

  /**
   * @return CGroups
   */
  function loadRefGroup(){
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @return CDailyCheckItemCategory
   */
  function loadRefCategory(){
    return $this->_ref_category = $this->loadFwdRef("category_id", true);
  }

  /**
   * @param array $where
   * @param null  $order
   * @param null  $limit
   * @param null  $groupby
   * @param array $ljoin
   *
   * @return self[]
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $where['group_id'] = "= '".CGroups::loadCurrent()->_id."' OR group_id IS NULL";
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }
}
