<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CUserLog Class
 */
class CUserLog extends CMbMetaObject {
  // DB Table key
  var $user_log_id = null;

  // DB Fields
  var $user_id      = null;
  var $date         = null;
  var $type         = null;
  var $fields       = null;
  var $ip_address   = null;
  var $extra        = null;
  
  // Filter Fields
  var $_date_min    = null;
  var $_date_max    = null;
  
  // Object References
  var $_fields = null;
  var $_old_values = null;
  var $_ref_user = null;
  var $_canUndo = null;
  var $_undo = null;
  
  var $_merged_ids = null; // Tableau d'identifiants des objets fusionnÚs

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'user_log';
    $spec->key   = 'user_log_id';
    $spec->measureable = true;
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
  	$specs["object_id"]    = "ref notNull class|CMbObject meta|object_class unlink";
    $specs["object_class"] = "str notNull show|0"; // Ne pas mettre "class" !! (pour les CExObject)
    $specs["user_id"]      = "ref notNull class|CUser";
    $specs["date"]         = "dateTime notNull";
    $specs["type"]         = "enum notNull list|create|store|merge|delete";
    $specs["fields"]       = "text show|0";
    $specs["ip_address"]   = "ipAddress";
    $specs["extra"]        = "text show|0";

    $specs["_date_min"]    = "dateTime";
    $specs["_date_max"]    = "dateTime moreEquals|_date_min";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->fields) {
      $this->_fields = explode(" ", $this->fields);
    }
  }
  
  function updateDBFields() {
    parent::updateDBFields();
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
      $date_max = mbDateTime("+3 seconds", $this->date);
      $where = array(
        "user_id" => "= '$this->user_id'",
        "type" => " = 'delete'",
        "date" => "BETWEEN '$this->date' AND '$date_max'"
      );
      $logs = $this->loadList($where);
      
      foreach($logs as $_log){
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
      "object_class" => "= '$object->_class_name'",
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
		foreach($this->_old_values as $_field => $_value) {
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
}
