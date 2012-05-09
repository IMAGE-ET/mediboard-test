<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// ------------------------------------
return;  // DON'T LOAD THIS CLASS YET
// ------------------------------------

class CObjectConfig extends CMbMetaObject {
  static $_deletion_value = "@@DELETE@@";
  
  var $object_config_id = null;
  
  var $key   = null;
  var $value = null;
  
  /**
   * Default values
   */
  var $_default = array(
    //"transmissions_hours"   => 24,
    //"cible_mandatory_trans" => 0,
  );
  
  /**
   * Inheritance schema, from the "bigger" class to the most precise
   */
  var $_inherit = array(
    //"CGroups"  => array(),
    //"CService" => array("group_id"),
    //"CChambre" => array("service_id", "group_id"),
  );
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key      = "object_config_id";
    $spec->loggable = false;
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["key"]   = "str notNull";
    $props["value"] = "str";
    return $props;
  }
  
  /**
   * @param array Configs
   * @param CMbObject Object to get the configs of
   * 
   * @return CMbObject The resolved next object
   */
  protected function inheritConfig(&$configs, CMbObject $object) {
    if (!$object) {
      return;
    }
    
    $new_object = null;
    
    foreach($this->_inherit[$object->_class] as $_fwd) {
      $object = $object->loadFwdRef($_fwd);
      
      if (!$new_object) {
        $new_object = $object;
      }
    }
    
    $configs = array_merge($configs, $this->loadConfigs($object->_class, $object->_id));
    
    return $new_object;
  }
  
  /**
   * @param string  Object class
   * @param integer Object ID
   * 
   * @return array
   */
  protected function loadConfigs($object_class = null, $object_id = null) {
    $where = array();
    
    if ($object_class && $object_id) {
      $where["object_class"] = "= '$object_class'";
      $where["object_id"]    = "= '$object_id'";
    }
    else {
      $where["object_class"] = "IS NULL";
      $where["object_id"]    = "IS NULL";
    }
    
    $request = new CRequest;
    $request->addWhere($where);
    $request->addSelect(array("key", "value"));
    
    return $this->_spec->ds->loadHashList($request->getRequest());
  }
  
  /**
   * @param CMbObject Object
   * 
   * @return array The corresponding configs
   */
  function getConfig(CMbObject $object = null){
    $configs = array_merge($this->_default, $this->loadConfigs(/* default */));
    
    while($object = $this->inheritConfig($configs, $object));
    
    return $configs;
  }
  
  /**
   * @param array Configs
   * @param CMbObject Object
   */
  function setConfigs($configs, CMbObject $object = null) {
    foreach($configs as $_key => $_value) {
      $_config = new self;
      $_config->key = $_key;
      $_config->setObject($object);
      $_config->loadMatchingObject();
      
      if ($_config->_id && $_value === self::$_deletion_value) {
        $_config->delete();
      }
      else {
        $_config->value = $_value;
        $_config->store();
      }
    }
  }
}
