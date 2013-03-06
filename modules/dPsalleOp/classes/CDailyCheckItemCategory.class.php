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
  public $target_class;
  public $target_id;
  public $type;

  /** @var CDailyCheckItemType[] */
  public $_ref_item_types;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_target;

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
    $props['target_class'] = 'enum list|CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire notNull default|CSalle';
    $props['target_id']    = 'ref class|CMbObject meta|target_class';
    $props['type']  = 'enum list|'.implode('|', array_keys(CDailyCheckList::$types));
    $props['desc']  = 'text';
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

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = ($this->target_class == 'CBlocOperatoire' ? 'Salle de réveil' : $this->getLocale("target_class"))." - $this->title";
  }

  /**
   * @return array
   */
  static function getCategoriesTree(){
    $item_category = new self();

    $target_classes = array_keys($item_category->_specs["target_class"]->_locales);
    foreach (CDailyCheckList::$_HAS_classes as $_class) {
      unset($target_classes[$_class]);
    }

    $targets = array();
    $item_categories_by_class = array();

    foreach ($target_classes as $_class) {
      /** @var CMbObject $_object */
      $_object = new $_class;
      $_targets = $_object->loadGroupList();
      array_unshift($_targets, $_object);

      $targets[$_class] = array_combine(CMbArray::pluck($_targets, "_id"), $_targets);

      $where = array(
        "target_class" => "= '$_class'",
      );

      /** @var CDailyCheckItemCategory[] $_list */
      $_list = $item_category->loadList($where, 'target_id+0, type, title'); // target_id+0 to have NULL at the beginning

      $item_categories_by_object = array();
      foreach ($_list as $_category) {
        $_key = $_category->target_id ? $_category->target_id : "all";
        $item_categories_by_object[$_key][$_category->_id] = $_category;
      }

      $item_categories_by_class[$_class] = $item_categories_by_object;
    }

    return array(
      $targets,
      $item_categories_by_class,
    );
  }
}
