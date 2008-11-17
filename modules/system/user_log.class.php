<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
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
  
  // Filter Fields
  var $_date_min	 			= null;
  var $_date_max 				= null;
  
  // Object References
  var $_fields = null;
  var $_ref_user = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'user_log';
    $spec->key   = 'user_log_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
  	$specs["object_id"]    = "notNull ref class|CMbObject meta|object_class unlink";
    $specs["user_id"]      = "notNull ref class|CUser";
    $specs["date"]         = "notNull dateTime";
    $specs["type"]         = "notNull enum list|create|store|merge|delete";
    $specs["fields"]       = "text";

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
}
?>