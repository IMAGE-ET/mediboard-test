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

/**
 * Check list type
 */
class CDailyCheckListType extends CMbObject {
  public $daily_check_list_type_id;

  public $object_class;
  //public $object_id; // @todo REMOVE
  public $group_id;
  public $title;
  public $type_validateur;
  public $description;

  public $_object_guid;
  public $_duplicate;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_object;

  /** @var CDailyCheckItemCategory[] */
  public $_ref_categories;

  /** @var CGroups */
  public $_ref_group;

  /** @var CDailyCheckListTypeLink[] */
  public $_ref_type_links;

  public $_links = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list_type';
    $spec->key   = 'daily_check_list_type_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['object_class'] = 'enum notNull list|CSalle|CBlocOperatoire default|CSalle';
    //$props['object_id']    = 'ref class|CMbObject meta|object_class autocomplete';
    $props['group_id']     = 'ref notNull class|CGroups';
    $props['title']        = 'str notNull';
    $props['type_validateur'] = "set vertical list|chir|anesth|op|op_panseuse|reveil|service|iade|brancardier|sagefemme|manipulateur";
    $props['description']  = 'text';

    $props['_object_guid'] = 'str';
    $props['_links']       = 'str';
    $props['_duplicate']   = 'bool default|0';
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps(){
    $backProps = parent::getBackProps();
    $backProps["daily_check_list_categories"] = "CDailyCheckItemCategory list_type_id";
    $backProps["daily_check_lists"]           = "CDailyCheckList list_type_id";
    $backProps["daily_check_list_type_links"] = "CDailyCheckListTypeLink list_type_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->title;
  }

  /**
   * Load group
   *
   * @return CGroups
   */
  function loadRefGroup(){
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * Load categories
   *
   * @return CDailyCheckItemCategory[]
   */
  function loadRefsCategories(){
    return $this->_ref_categories = $this->loadBackRefs("daily_check_list_categories", "`index`, title");
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($this->_duplicate == 1) {
      $this->duplicate();
      return;
    }

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

    return null;
  }

  /**
   * Load type links
   *
   * @return CDailyCheckListTypeLink[]
   */
  function loadRefTypeLinks(){
    return $this->_ref_type_links = $this->loadBackRefs("daily_check_list_type_links", "object_class, object_id+0");
  }

  /**
   * Make an array of links
   *
   * @return array
   */
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
   * Get list types tree
   *
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

  /**
   * Duplicate CheckList
   *
   * @return void
   */
  function duplicate(){
    $checklist = new CDailyCheckListType();
    $checklist->object_class  = $this->object_class;
    $checklist->group_id      = $this->group_id;
    $checklist->title         = $this->title." dupliqué";
    $checklist->type_validateur = $this->type_validateur;
    $checklist->description   = $this->description;

    if ($msg = $checklist->store()) {
      return $msg;
    }

    foreach ($this->loadRefTypeLinks() as $link) {
      $_link = $link;
      $_link->_id  = "";
      $_link->list_type_id  = $checklist->_id;
      if ($msg = $_link->store()) {
        return $msg;
      }
    }

    foreach ($this->loadRefsCategories() as $categorie) {
      $items = $categorie->loadRefItemTypes();
      $new_categorie = $categorie;
      $new_categorie->_id  = "";
      $new_categorie->list_type_id  = $checklist->_id;
      if ($msg = $new_categorie->store()) {
        return $msg;
      }
      foreach ($items as $item) {
        $new_item = $item;
        $new_item->_id  = "";
        $new_item->category_id  = $new_categorie->_id;
        if ($msg = $new_item->store()) {
          return $msg;
        }
      }
    }
  }
}
