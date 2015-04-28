<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Check list group
 */
class CDailyCheckListGroup extends CMbObject
{
  public $check_list_group_id;

  public $group_id;
  public $title;
  public $description;
  public $actif;

  public $_type_has;
  public $_duplicate;

  /** @var CGroups */
  public $_ref_group;

  /** @var CDailyCheckListType[] */
  public $_ref_check_liste_types;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list_group';
    $spec->key   = 'check_list_group_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['group_id']    = 'ref notNull class|CGroups';
    $props['title']       = 'str notNull';
    $props['description'] = 'text';
    $props['actif']       = 'bool default|1';

    $props['_type_has'] = 'text';
    $props['_duplicate'] = 'bool';
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["check_list_group"] = "CDailyCheckListType check_list_group_id";
    return $backProps;
  }

  /**
   * @see parent::store()
   */
  function store() {
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->_duplicate && $this->_type_has) {
      $this->duplicate();
    }
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
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * Load group
   *
   * @return CDailyCheckListType[]
   */
  function loadRefChecklist() {
    return $this->_ref_check_liste_types = $this->loadBackRefs("check_list_group");
  }

  /**
   * Duplicate checklist HAS
   *
   * @return void|string
   */
  function duplicate() {
    $types_checklist = array_intersect(CDailyCheckList::$types, array($this->_type_has));
    foreach ($types_checklist as $type_name => $type) {
      $checklist_type = new CDailyCheckListType();
      $checklist_type->group_id             = $this->group_id;
      $checklist_type->check_list_group_id  = $this->_id;
      $checklist_type->type                 = 'intervention';
      $checklist_type->title                = CAppUI::tr("CDailyCheckItemCategory.type.".$type_name);
      $checklist_type->description          = CAppUI::tr("CDailyCheckList.$type.$type_name.small");
      $checklist_type->type_validateur      = "chir_interv|op|op_panseuse|iade|sagefemme|manipulateur";

      if ($msg = $checklist_type->store()) {
        return $msg;
      }

      $where = array();
      $where["type"] = " = '$type_name'";
      $where["target_class"] = " = 'COperation'";
      $where["list_type_id"] = " IS NULL";
      $_categorie = new CDailyCheckItemCategory();

      foreach ($_categorie->loadList($where, "title") as $categorie) {
        /* @var CDailyCheckItemCategory $categorie*/
        $items = $categorie->loadRefItemTypes();
        $new_categorie = $categorie;
        $new_categorie->_id  = "";
        $new_categorie->list_type_id  = $checklist_type->_id;
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
    return null;
  }

  static function loadChecklistGroup(){
    $list_group = new self;
    $list_group->group_id = CGroups::loadCurrent()->_id;
    $list_group->actif    = 1;
    $checklists_group = $list_group->loadMatchingList("title");
    foreach ($checklists_group as $check_group) {
      $check_group->loadRefChecklist();
    }

    return $checklists_group;
  }
}
