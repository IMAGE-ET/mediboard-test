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
  var $_ref_user = null;
  
  var $_merged_ids = null; // Tableau d'identifiants des objets fusionnés

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
    $specs["user_id"]      = "ref notNull class|CUser";
    $specs["date"]         = "dateTime notNull";
    $specs["type"]         = "enum notNull list|create|store|merge|delete";
    $specs["fields"]       = "text";
    $specs["ip_address"]   = "ipAddress";
    $specs["extra"]        = "text";

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
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
  	$user = new CUser;
    $this->_ref_user = $user->getCached($this->user_id);
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
}
?>