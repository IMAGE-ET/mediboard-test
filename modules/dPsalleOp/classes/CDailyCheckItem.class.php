<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CDailyCheckItem extends CMbObject {
  public $daily_check_item_id;

  // DB Fields
  public $list_id;
  public $item_type_id;
  public $checked;
  
  /** @var CDailyCheckList */
  public $_ref_list;

  /** @var CDailyCheckItemType */
  public $_ref_item_type;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item';
    $spec->key   = 'daily_check_item_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['list_id']      = 'ref notNull class|CDailyCheckList';
    $props['item_type_id'] = 'ref notNull class|CDailyCheckItemType';
    $props['checked']      = 'enum notNull list|yes|no|nr|na';
    return $props;
  }
  
  function getAnswer(){
    return $this->getFormattedValue("checked");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "$this->_ref_item_type (".$this->getAnswer().")";
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefList();
    $this->loadRefItemType();
  }

  /**
   * Get check list
   *
   * @return CDailyCheckList
   */
  function loadRefList(){
    return $this->_ref_list = $this->loadFwdRef("list_id", true);
  }

  /**
   * Get item type
   *
   * @return CDailyCheckItemType
   */
  function loadRefItemType(){
    return $this->_ref_item_type = $this->loadFwdRef("item_type_id", true);
  }
}
