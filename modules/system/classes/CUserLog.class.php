<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CUserLog Class
 */
class CUserLog extends CMbMetaObject {
  // DB Table key
  public $user_log_id;

  // DB Fields
  public $user_id;
  public $date;
  public $type;
  public $fields;
  public $ip_address;
  public $extra;
  
  // Filter Fields
  public $_date_min;
  public $_date_max;
  
  // Object References
  public $_fields;
  public $_old_values;
  public $_ref_user;
  public $_canUndo;
  public $_undo;
  
  public $_merged_ids; // Tableau d'identifiants des objets fusionnés

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'user_log';
    $spec->key   = 'user_log_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_id"]    = "ref notNull class|CMbObject meta|object_class unlink";
    $props["object_class"] = "str notNull show|0"; // Ne pas mettre "class" !! (pour les CExObject)
    $props["user_id"]      = "ref notNull class|CUser";
    $props["date"]         = "dateTime notNull";
    $props["type"]         = "enum notNull list|create|store|merge|delete";
    $props["fields"]       = "text show|0";
    $props["ip_address"]   = "ipAddress";
    $props["extra"]        = "text show|0";

    $props["_date_min"]    = "dateTime";
    $props["_date_max"]    = "dateTime moreEquals|_date_min";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->fields) {
      $this->_fields = explode(" ", $this->fields);
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();
    if ($this->_fields) {
      $this->fields = implode(" ", $this->_fields);
    }
  }

  /**
   * Gets old values (before the change happened)
   *
   * @return array
   */
  function getOldValues() {
    $this->completeField("extra");
    
    $this->_old_values = array();
    if ($this->extra && ($this->type === "store" || $this->type === "merge")) {
      $this->_old_values = (array) json_decode($this->extra);
      $this->_old_values = array_map("utf8_decode", $this->_old_values);
    }
    return $this->_old_values;
  }
  
  /**
   * Load the user who did the change
   *
   * @param bool $cache Use object cache
   *
   * @return CUser
   */
  function loadRefUser($cache = true) {
    return $this->_ref_user = $this->loadFwdRef("user_id", $cache);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefUser();
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    
    $this->getOldValues();
    $this->canUndo();
    $this->loadTargetObject()->loadHistory();
  }

  /**
   * Gets all the IDs implied in the merging
   *
   * @return ref[]
   */
  function loadMergedIds(){
    if ($this->type === "merge") {
      $date_max = CMbDT::dateTime("+3 seconds", $this->date);
      $where = array(
        "user_id" => "= '$this->user_id'",
        "type" => " = 'delete'",
        "date" => "BETWEEN '$this->date' AND '$date_max'"
      );

      /** @var self[] $logs */
      $logs = $this->loadList($where);
      
      foreach ($logs as $_log) {
        $this->_merged_ids[] = $_log->object_id;
      }
    }
  }

  /**
   * Counts the recent user logs
   *
   * @param string   $object_class The object class
   * @param ref[]    $ids          The list of IDs
   * @param datetime $recent       The date considered as recent
   *
   * @return int
   */
  static function countRecentFor($object_class, $ids, $recent){
    $log = new CUserLog();
    $where = array();
    $where["object_class"] = "= '$object_class'";
    $where["date"] = "> '$recent'";
    $where["object_id"] = CSQLDataSource::prepareIn($ids);
    return $log->countList($where);
  }

  /**
   * Gets the object value at a specific date
   *
   * @param CMbObject $object The object to get the value of
   * @param datetime  $date   The date
   * @param string    $field  Field name
   *
   * @return mixed
   */
  static function getObjectValueAtDate(CMbObject $object, $date, $field) {
    $where = array(
      "object_class" => "= '$object->_class'",
      "object_id"    => "= '$object->_id'",
      "type"         => "IN('store', 'merge')",
      "extra IS NOT NULL AND extra != '[]'",
    );
    
    if ($date) {
      $where["date"] = ">= '$date'";
    }
    
    $where[] = "
      fields LIKE '$field' OR 
      fields LIKE '$field %' OR 
      fields LIKE '% $field' OR 
      fields LIKE '% $field %'";
    
    $user_log = new self;
    $user_log->loadObject($where, "date ASC");
    
    if ($user_log->_id) {
      $user_log->getOldValues();
    }
    
    return CValue::read($user_log->_old_values, $field, $object->$field);
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($msg = $this->check()) {
      return $msg;
    }
    
    if ($this->_undo) {
      $this->_undo = null;
      return $this->undo();
    }
    
    return parent::store();
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx(){
    if (!$this->canEdit() || !$this->_ref_module->canAdmin()) {
      return false;
    }
    
    return parent::canDeleteEx();
  }

  /**
   * Tells if we can undo the change
   *
   * @return bool
   */
  function canUndo(){
    $this->completeField("type", "extra");
    
    if (!$this->_id || ($this->type != "store") || ($this->extra == null) || !$this->canEdit() || !$this->_ref_module->canAdmin()) {
      return $this->_canUndo = false;
    }
    
    $this->completeField("object_id", "object_class");
    
    $where = array(
      "object_id"           => "= '$this->object_id'",
      "object_class"        => "= '$this->object_class'",
      "{$this->_spec->key}" => "> '$this->_id'",
    );
    
    return $this->_canUndo = ($this->countList($where) == 0);
  }

  /**
   * Undo the change
   *
   * @return null|string
   */
  function undo(){
    if (!$this->canUndo()) {
      return "CUserLog-undo-ko";
    }
    
    $object = $this->loadTargetObject();
    $object->_spec->loggable = false;
    
    $this->getOldValues();
    
    // Revalue fields
    foreach ($this->_old_values as $_field => $_value) {
      $object->$_field = $_value;
    }
    $object->updateFormFields();
    
    // Prevent disturbing checks
    $object->_merging = true;
    
    $msg = $object->store();
    $object->_spec->loggable = true;
    
    if ($msg) {
      return $msg;
    }
    
    return $this->delete();
  }

  /**
   * Load period aggregation for the system view
   *
   * @param datetime $start        Datetime where the search starts
   * @param datetime $end          Datetime where the search ends
   * @param string   $period       Aggregation period
   * @param string   $type         User log type to filter
   * @param int      $user_id      User ID to filter
   * @param string   $object_class Class to filter
   * @param int      $object_id    Object ID to filter
   *
   * @return array|bool
   */
  static function loadPeriodAggregation(
      $start,
      $end,
      $period,
      $type         = null,
      $user_id      = null,
      $object_class = null,
      $object_id    = null
  ) {
    switch ($period) {
      default:
      case "hour":
        $period_format = "%d-%m-%Y %Hh";
        break;

      case "day":
        $period_format = "%d-%m-%Y";
        break;

      case "week":
        $period_format = "%Y Sem. %u";
        break;

      case "month":
        $period_format = "%m-%Y";
        break;

      case "year":
        $period_format = "%Y";
    }

    $query = "SELECT
        COUNT(*) AS count,
      DATE_FORMAT(`date`, '$period_format') AS `gperiod`
      FROM `user_log`
      USE INDEX (date)
      WHERE `date` >= '$start'";

    if ($end) {
      $query .= "\nAND `date` <= '$end'";
    }

    if ($type) {
      $query .= "\nAND `type` = '$type'";
    }

    if ($user_id) {
      $query .= "\nAND `user_id` = '$user_id'";
    }

    if ($object_class) {
      $query .= "\nAND `object_class` = '$object_class'";
    }

    if ($object_id) {
      $query .= "\nAND `object_id` = '$object_id'";
    }

    $query .= "\nGROUP BY `gperiod` ORDER BY `date`";

    $log = new self;
    return $log->_spec->ds->loadList($query);
  }
}
