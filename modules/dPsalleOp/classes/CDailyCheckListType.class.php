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
  //public $object_id; // @todo REMOVE
  public $group_id;
  public $title;
  public $description;

  public $_object_guid;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_object;

  /** @var CDailyCheckItemCategory[] */
  public $_ref_categories;

  /** @var CGroups */
  public $_ref_group;

  /** @var CDailyCheckListTypeLink[] */
  public $_ref_type_links;

  public $_links = array();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list_type';
    $spec->key   = 'daily_check_list_type_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['object_class'] = 'enum notNull list|CSalle|CBlocOperatoire default|CSalle';
    //$props['object_id']    = 'ref class|CMbObject meta|object_class autocomplete';
    $props['group_id']     = 'ref notNull class|CGroups';
    $props['title']        = 'str notNull';
    $props['description']  = 'text';
    $props['_object_guid'] = 'str';
    $props['_links']       = 'str';
    return $props;
  }

  function getBackProps(){
    $backProps = parent::getBackProps();
    $backProps["daily_check_list_categories"] = "CDailyCheckItemCategory list_type_id";
    $backProps["daily_check_lists"]           = "CDailyCheckList list_type_id";
    $backProps["daily_check_list_type_links"] = "CDailyCheckListTypeLink list_type_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->title;
  }

  /**
   * @return CGroups
   */
  function loadRefGroup(){
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @return CDailyCheckItemCategory[]
   */
  function loadRefsCategories(){
    return $this->_ref_categories = $this->loadBackRefs("daily_check_list_categories", "title");
  }

  function store(){
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->_links) {
      $current_links = $this->loadRefTypeLinks();
      $this->completeField("object_class");

      // Suppression des liens ayant un object class different de $this->object_class ou dont l'ID n'est pas dans la liste
      foreach ($current_links as $_link_object) {
        if (
            $_link_object->object_class != $this->object_class ||
            !in_array("$_link_object->object_class-$_link_object->object_id", $this->_links)
        ) {
          $_link_object->delete();
        }
      }

      // Creation des liens manquants
      foreach ($this->_links as $_object_guid) {
        list($_object_class, $_object_id) = explode("-", $_object_guid);

        // Exclude types from other class
        if ($_object_class !== $this->object_class) {
          continue;
        }

        $new_link = new CDailyCheckListTypeLink();
        $new_link->object_class = $_object_class;
        $new_link->object_id    = ($_object_id == "none" ? "" : $_object_id); // "" is important here !
        $new_link->list_type_id = $this->_id;
        $new_link->loadMatchingObject(); // Should never match
        $new_link->store();
      }
    }
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
      /** @var CSalle|CBlocOperatoire $_object */
      $_object = new $_class;
      //$_targets = $_object->loadGroupList();
      $_targets = $_object->loadList();
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
   * @return CDailyCheckListTypeLink[]
   */
  function loadRefTypeLinks(){
    return $this->_ref_type_links = $this->loadBackRefs("daily_check_list_type_links", "object_class, object_id+0");
  }

  function makeLinksArray(){
    $this->_links = array();

    $type_links = $this->loadRefTypeLinks();
    foreach ($type_links as $_link) {
      $_guid = $_link->loadRefObject()->_guid;
      $this->_links[$_guid] = $_guid;
    }

    return $this->_links;
  }

  /**
   * @return array
   */
  static function getListTypesTree(){
    $object = new self();

    $target_classes = CDailyCheckList::getNonHASClasses();
    $group_id = CGroups::loadCurrent()->_id;

    $targets = array();
    $by_class = array();

    foreach ($target_classes as $_class) {
      /** @var CSalle|CBlocOperatoire $_object */
      $_object = new $_class;
      $_targets = $_object->loadGroupList();
      array_unshift($_targets, $_object);

      $targets[$_class] = array_combine(CMbArray::pluck($_targets, "_id"), $_targets);

      $where = array(
        "object_class" => "= '$_class'",
        "group_id"     => "= '$group_id'",
      );

      $by_class[$_class] = $object->loadList($where, "title");
    }

    return array(
      $targets,
      $by_class,
    );
  }
}
