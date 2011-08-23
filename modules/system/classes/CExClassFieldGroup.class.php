<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassFieldGroup extends CMbObject {
  var $ex_class_field_group_id = null;
  
  var $ex_class_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  
  var $_ref_ex_class = null;
  var $_ref_fields = null;
  var $_ref_messages = null;
  var $_ref_host_fields = null;
  
  static $_fields_cache = array();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_group";
    $spec->key   = "ex_class_field_group_id";
    $spec->uniques["name"] = array("ex_class_id", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref class|CExClass cascade";
    $props["name"]        = "str notNull";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField ex_group_id";
    $backProps["host_fields"]  = "CExClassHostField ex_group_id";
    $backProps["class_messages"] = "CExClassMessage ex_group_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->name;
  }
  
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }
  
  function loadRefsFields($cache = true){
    if ($cache && isset(self::$_fields_cache[$this->_id])) {
      return $this->_ref_fields = self::$_fields_cache[$this->_id];
    }
    
    $this->_ref_fields = $this->loadBackRefs("class_fields");
    
    if ($cache) {
      self::$_fields_cache[$this->_id] = $this->_ref_fields;
    }
    
    return $this->_ref_fields;
  }
  
  function loadRefsHostFields(){
    return $this->_ref_host_fields = $this->loadBackRefs("host_fields");
  }
  
  function loadRefsMessages(){
    return $this->_ref_messages = $this->loadBackRefs("class_messages");
  }
}
