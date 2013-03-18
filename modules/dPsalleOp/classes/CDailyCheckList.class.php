<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDailyCheckList extends CMbObject { // not a MetaObject, as there can be multiple objects for different dates
  public $daily_check_list_id;

  // DB Fields
  public $date;
  public $object_class;
  public $object_id;
  public $type;
  public $comments;
  public $validator_id;
  public $list_type_id;

  /** @var CMediusers */
  public $_ref_validator;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_object;

  /** @var CDailyCheckItemType[] */
  public $_ref_item_types;

  /** @var CDailyCheckListType */
  public $_ref_list_type;

  public $_items;
  public $_validator_password;
  public $_readonly;
  public $_date_min;
  public $_date_max;

  static $types = array(
    // Secu patient
    "preanesth" => "normal",
    "preop"     => "normal",
    "postop"    => "normal",

    // Endoscopie digestive
    "preendoscopie"  => "endoscopie",
    "postendoscopie" => "endoscopie",

    // Endoscopie bronchique
    "preendoscopie_bronchique"  => "endoscopie-bronchique",
    "postendoscopie_bronchique" => "endoscopie-bronchique",

    // Radiologie interventionnelle
    "preanesth_radio" => "radio",
    "preop_radio"     => "radio",
    "postop_radio"    => "radio",

    // Pose dispositif vasculaire
    "disp_vasc_avant"   => "disp-vasc",
    "disp_vasc_pendant" => "disp-vasc",
    "disp_vasc_apres"   => "disp-vasc",
  );

  static $_HAS_classes = array(
    "COperation",
    "CPoseDispositifVasculaire",
  );

  static $_HAS_lists = array(
    "normal"                => "Au bloc opératoire (v. 2011-01)",
    "endoscopie"            => "En endoscopie digestive (v. 2013-01)",
    "endoscopie-bronchique" => "En endoscopie bronchique (v. 2011-01)",
    "radio"                 => "En radiologie interv. (v. 2011-01)",
  );

  /**
   * Get non-HAS classes
   *
   * @return array
   */
  static function getNonHASClasses(){
    static $check_list = null;
    if ($check_list === null) {
      $check_list = new self;
    }

    $target_classes = array_keys($check_list->_specs["object_class"]->_locales);
    $target_classes = array_diff($target_classes, CDailyCheckList::$_HAS_classes);

    return $target_classes;
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list';
    $spec->key   = 'daily_check_list_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['date']         = 'date notNull';
    $props['object_class'] = 'enum list|CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire notNull default|CSalle';
    $props['object_id']    = 'ref class|CMbObject meta|object_class notNull autocomplete';
    $props['list_type_id'] = 'ref class|CDailyCheckListType';
    $props['type']         = 'enum list|'.implode('|', array_keys(CDailyCheckList::$types));
    $props['validator_id'] = 'ref class|CMediusers';
    $props['comments']     = 'text';
    $props['_validator_password'] = 'password notNull';
    $props['_date_min'] = 'date';
    $props['_date_max'] = 'date';
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['items'] = 'CDailyCheckItem list_id';
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "$this->_ref_object le $this->date ($this->_ref_validator)";
  }

  /**
   * @return bool
   */
  function isReadonly() {
    $this->completeField("validator_id", "date");
    return $this->_readonly = ($this->_id && $this->validator_id && $this->date);
  }

  /**
   * @return CMediusers
   */
  function loadRefValidator(){
    return $this->_ref_validator = $this->loadFwdRef("validator_id", true);
  }

  function loadRefsFwd() {
    if ($this->object_class) {
      $this->_ref_object = $this->loadFwdRef("object_id", true);
    }

    $this->loadRefValidator();
  }

  function store() {
    if ($this->validator_id) {
      // Verification du mot de passe
      if ($this->_validator_password) {
        $this->loadRefsFwd();
        if (!CUser::checkPassword($this->_ref_validator->_user_username, $this->_validator_password)) {
          $this->validator_id = "";
          return 'Le mot de passe entré n\'est pas correct';
        }
      }

      // Validator_id passé mais il ne faut pas l'enregistré
      /** @var self $old */
      $old = $this->loadOldObject();
      if (!$this->_validator_password && !$old->validator_id) {
        $this->validator_id = "";
      }
    }

    if ($msg = parent::store()) {
      return $msg;
    }

    // Sauvegarde des items cochés
    $items = $this->_items ? $this->_items : array();

    $this->loadItemTypes();
    foreach ($this->_ref_item_types as $type) {
      $check_item = new CDailyCheckItem();
      $check_item->list_id = $this->_id;
      $check_item->item_type_id = $type->_id;
      $check_item->loadMatchingObject();
      $check_item->checked = (isset($items[$type->_id]) ? $items[$type->_id] : "");
      if ($check_item->checked == "") {
        mbTrace($check_item);
      }
      if ($msg = $check_item->store()) {
        return $msg;
      }
    }
  }

  /**
   * @param CMbObject $object
   * @param string    $date
   * @param string    $type
   *
   * @return self
   */
  static function getList(CMbObject $object, $date = null, $type = null, $list_type_id = null){
    $list = new self;
    $list->object_class = $object->_class;
    $list->object_id = $object->_id;
    $list->list_type_id = $list_type_id;
    $list->date = $date;
    $list->type = $type;
    $list->loadRefsFwd();
    $list->loadMatchingObject();
    $list->loadRefListType()->loadRefsCategories();
    $list->isReadonly();
    return $list;
  }

  /**
   * @return CDailyCheckListType
   */
  function loadRefListType(){
    return $this->_ref_list_type = $this->loadFwdRef("list_type_id");
  }

  static function getRooms() {
    $list_rooms = array(
      "CSalle"          => array(),
      "CBlocOperatoire" => array(),
    );

    foreach ($list_rooms as $class => &$list) {
      /** @var CSalle|CBlocOperatoire $room */
      $room = new $class;
      $list = $room->loadGroupList();

      /** @var CSalle|CBlocOperatoire $empty */
      $empty = new $class;
      $empty->updateFormFields();
      array_unshift($list, $empty);
    }

    return $list_rooms;
  }

  /**
   * @return CDailyCheckItem[]
   */
  function loadItemTypes() {
    $ds = $this->getDS();

    $where = array(
      "active" => "= '1'",
    );
    if ($this->type) {
      $where['daily_check_item_category.type'] = $ds->prepare("= %", $this->type);
    }
    $ljoin = array(
      'daily_check_item_category' => 'daily_check_item_category.daily_check_item_category_id = daily_check_item_type.category_id'
    );

    if ($this->list_type_id) {
      $where["daily_check_item_category.list_type_id"] = $ds->prepare("= %", $this->list_type_id);
    }
    else {
      $where["daily_check_item_category.target_class"] = $ds->prepare("= %", $this->object_class);
      $where[] = $ds->prepare("daily_check_item_category.target_id IS NULL OR daily_check_item_category.target_id = %", $this->object_id);
    }

    $orderby = 'daily_check_item_category.title, ';

    // Si liste des points de la HAS
    if (in_array($this->object_class, self::$_HAS_classes)) {
      $orderby .= "daily_check_item_type_id";
    }
    else {
      $orderby .= "`index`, title";
    }

    $itemType = new CDailyCheckItemType();

    $this->_ref_item_types = $itemType->loadGroupList($where, $orderby, null, null, $ljoin);
    foreach ($this->_ref_item_types as $type) {
      $type->loadRefsFwd();
    }

    /** @var CDailyCheckItem[] $items */
    $items = $this->loadBackRefs('items');

    if ($items) {
      foreach ($items as $item) {
        if (isset($this->_ref_item_types[$item->item_type_id])) {
          $this->_ref_item_types[$item->item_type_id]->_checked = $item->checked;
          $this->_ref_item_types[$item->item_type_id]->_answer = $item->getAnswer();
        }
      }
    }

    return $this->_ref_item_types;
  }
}
