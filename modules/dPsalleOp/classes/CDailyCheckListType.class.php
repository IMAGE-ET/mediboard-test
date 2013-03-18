<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDailyCheckListType extends CMbObject {
  public $daily_check_list_type_id;

  public $object_class;
  public $object_id;
  public $title;
  public $description;

  public $_object_guid;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_object;

  /** @var CDailyCheckItemCategory[] */
  public $_ref_categories;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list_type';
    $spec->key   = 'daily_check_list_type_id';
    $spec->uniques["object"] = array("object_class", "object_id", "title");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['object_class'] = 'enum notNull list|CSalle|CBlocOperatoire default|CSalle';
    $props['object_id']    = 'ref class|CMbObject meta|object_class autocomplete';
    $props['title']        = 'str notNull';
    $props['description']  = 'text';
    $props['_object_guid'] = 'str';
    return $props;
  }

  function getBackProps(){
    $backProps = parent::getBackProps();
    $backProps["daily_check_list_categories"] = "CDailyCheckItemCategory list_type_id";
    $backProps["daily_check_lists"]           = "CDailyCheckList list_type_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->title;
  }

  /**
   * @return CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire
   */
  function loadRefObject(){
    return $this->_ref_object = $this->loadFwdRef("object_id", true);
  }

  /**
   * @return CDailyCheckItemCategory[]
   */
  function loadRefsCategories(){
    return $this->_ref_categories = $this->loadBackRefs("daily_check_list_categories", "title");
  }

  /**
   * @param string $class              Class name
   * @param string $object_class_field
   * @param string $object_id_field
   *
   * @return array
   */
  static function getObjectsTree($class, $object_class_field = "object_class", $object_id_field = "object_id") {
    /** @var CDailyCheckListType|CDailyCheckItemCategory $object */
    $object = new $class();

    $target_classes = CDailyCheckList::getNonHASClasses();

    $targets = array();
    $by_class = array();

    foreach ($target_classes as $_class) {
      /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire $_object */
      $_object = new $_class;
      $_targets = $_object->loadGroupList();
      array_unshift($_targets, $_object);

      $targets[$_class] = array_combine(CMbArray::pluck($_targets, "_id"), $_targets);

      $where = array(
        $object_class_field => "= '$_class'",
      );

      /** @var CDailyCheckListType[]|CDailyCheckItemCategory[] $_list */
      $_list = $object->loadList($where, "$object_id_field+0, title"); // target_id+0 to have NULL at the beginning

      $by_object = array();
      foreach ($_list as $_category) {
        $_key = $_category->$object_id_field ? $_category->$object_id_field : "all";
        $by_object[$_key][$_category->_id] = $_category;
      }

      $by_class[$_class] = $by_object;
    }

    return array(
      $targets,
      $by_class,
    );
  }

  /**
   * @return array
   */
  static function getListTypesTree(){
    return CDailyCheckListType::getObjectsTree("CDailyCheckListType");
  }
}
