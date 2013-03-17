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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'user_log';
    $spec->key   = 'user_log_id';
    $spec->measureable = true;
    return $spec;
  }

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
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->fields) {
      $this->_fields = explode(" ", $this->fields);
    }
  }
  
  function updatePlainFields() {
    parent::updatePlainFields();
    if ($this->_fields) {
      $this->fields = implode(" ", $this->_fields);
    }
  }
  
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
   * @param bool $cache [optional]
   *
   * @return CUser
   */
  function loadRefUser($cache = true) {
    return $this->_ref_user = $this->loadFwdRef("user_id", $cache);
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefUser();
  }
  
  function loadView(){
    parent::loadView();
    
    $this->getOldValues();
    $this->canUndo();
    $this->loadTargetObject()->loadHistory();
  }
  
  function loadMergedIds(){
    if ($this->type === "merge") {
      $date_max = CMbDT::dateTime("+3 seconds", $this->date);
      $where = array(
        "user_id" => "= '$this->user_id'",
        "type" => " = 'delete'",
        "date" => "BETWEEN '$this->date' AND '$date_max'"
      );
      $logs = $this->loadList($where);
      
      foreach ($logs as $_log) {
        $this->_merged_ids[] = $_log->object_id;
      }
    }
  }
   
  static function countRecentFor($object_class, $ids, $recent){
    $log = new CUserLog();
    $where = array();
    $where["object_class"] = "= '$object_class'";
    $where["date"] = "> '$recent'";
    $where["object_id"] = CSQLDataSource::prepareIn($ids);
    return $log->countList($where);
  }
  
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
  
  function canDeleteEx(){
    if (!$this->canEdit() || !$this->_ref_module->canAdmin()) {
      return false;
    }
    
    return parent::canDeleteEx();
  }
  
  function canUndo(){
    $this->completeField("type", "extra");
    
    if (!$this->_id || ($this->type != "store") || ($this->extra == null) || !$this->canEdit() || !$this->_ref_module->canAdmin()) {
      return $this->_canUndo = false;
    }
    
    $this->completeField("object_id", "object_class");
    
    $where = array(
      "object_id"           => "= '$this->object_id'",
      "object_class"        => "= '$this->object_class'",
      "{$this->_spec->key}" => "> $this->_id",
    );
    
    return $this->_canUndo = ($this->countList($where) == 0);
  }
  
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
  
  static function loadPeriodAggregation($start, $end, $period_format, $module_name, $action_name) {
    $query = "SELECT
        COUNT(*) AS count,
        `object_class`,
        `type`,
        `date`,
      DATE_FORMAT(`date`, '$period_format') AS `gperiod`
      FROM `user_log`
      USE INDEX (date)
      WHERE `date` BETWEEN '$start' AND '$end'";
          
    
    
    if ($action_name) {
      // If is_array, then we have to show one graph per class, with all types
      if (is_array($action_name)) {
        $_class = array_shift($action_name);
        $query .= "\nAND `object_class` = '$_class'";
        $query .= "\nAND `type` IN ('".implode("', '", $action_name)."') ";
        $query .= "\nGROUP BY `gperiod`, `object_class`, `type` ORDER BY `date`";
      }
      else {
        if ($module_name) {
          $listClasses = implode("', '", CModule::getClassesFor($module_name));
          $query .= "\nAND object_class IN ('".$listClasses."') ";
        }
        
        $query .= "\nAND `type` = '$action_name'";
        $query .= "\nGROUP BY `gperiod`, `object_class`, `type` ORDER BY `date`";
      }
    }
    else {
      if ($module_name) {
        $listClasses = implode("', '", CModule::getClassesFor($module_name));
        $query .= "\nAND object_class IN ('".$listClasses."') ";
      }
      
      $query .= "\nGROUP BY `gperiod` ORDER BY `date`";
    }
    
    $log = new self;
    return $log->_spec->ds->loadList($query);
  }
}
