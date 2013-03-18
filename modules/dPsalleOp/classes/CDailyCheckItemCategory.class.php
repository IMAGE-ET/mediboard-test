<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItemCategory extends CMbObject {
  public $daily_check_item_category_id;

  // DB Fields
  public $title;
  public $desc;

  ////////
  public $target_class;
  public $target_id;
  public $type;
  // OR //
  public $list_type_id;
  ////////

  /** @var CDailyCheckItemType[] */
  public $_ref_item_types;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_target;

  /** @var CDailyCheckListType */
  public $_ref_list_type;

  public $_target_guid;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item_category';
    $spec->key   = 'daily_check_item_category_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['title'] = 'str notNull';
    $props['desc']  = 'text';

    $props['target_class'] = 'enum list|CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire notNull default|CSalle';
    $props['target_id']    = 'ref class|CMbObject meta|target_class';
    $props['type']         = 'enum list|'.implode('|', array_keys(CDailyCheckList::$types));
    $props['list_type_id'] = 'ref class|CDailyCheckListType autocomplete|_view';

    $props['_target_guid'] = 'str notNull';
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['item_types'] = 'CDailyCheckItemType category_id';
    return $backProps;
  }

  /**
   * @return CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire
   */
  function loadRefTarget(){
    return $this->_ref_target = $this->loadFwdRef("target_id");
  }

  /**
   * @return CDailyCheckListType
   */
  function loadRefListType(){
    return $this->_ref_list_type = $this->loadFwdRef("list_type_id");
  }

  /**
   * @return CDailyCheckItemType[]
   */
  function loadRefItemTypes() {
    return $this->_ref_item_types = $this->loadBackRefs("item_types", "`index`, title");
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = ($this->target_class == 'CBlocOperatoire' ? 'Salle de réveil' : $this->getLocale("target_class"))." - $this->title";
  }

  /**
   * @return array
   */
  static function getCategoriesTree(){
    return CDailyCheckListType::getObjectsTree("CDailyCheckItemCategory", "target_class", "target_id");
  }
}
