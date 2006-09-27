<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

/**
 * The CUserLog Class
 */
class CUserLog extends CMbObject {
  // DB Table key
  var $user_log_id = null;

  // DB Fields
  var $user_id      = null;
  var $date         = null;
  var $object_id    = null;
  var $object_class = null;
  var $type         = null;
  var $fields       = null;

  // Object References
  var $_fields = null;
  var $_ref_user = null;
  var $_ref_object = null;

  function CUserLog() {
    $this->CMbObject("user_log", "user_log_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "user_id"      => "ref|notNull",
      "date"         => "dateTime|notNull",
      "object_id"    => "ref|notNull",
      "object_class" => "str|maxLength|25|notNull",
      "type"         => "enum|create|store|delete|notNull",
      "fields"       => "text"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables = array ();
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->fields) {
      $this->_fields = split(" ", $this->fields);
    }
  }
  
  function updateDBFields() {
    parent::updateDBFields();
    if ($this->_fields) {
      $this->fields = join($this->_fields, " ");
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_user = new CUser;
    $this->_ref_user->load($this->user_id);

    $this->_ref_object = new $this->object_class;
    if(!$this->_ref_object->load($this->object_id)) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprimé";
    }
  }
}
?>