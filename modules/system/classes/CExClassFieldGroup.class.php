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
  var $rank = null;
  
  var $_ref_ex_class = null;
  var $_ref_fields = null;
  var $_ref_messages = null;
  var $_ref_host_fields = null;
  
  var $_move = null;
  
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
    $props["rank"]        = "num min|0";
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
  
  /**
   * @return CExClass
   */
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
  
  function store(){
    if ($this->_move && $this->_id) {
      $this->completeField("ex_class_id");
      $groups = $this->loadRefExClass()->loadRefsGroups();
      $groups_ids = array_keys($groups);
      $self_index = array_search($this->_id, $groups_ids);
      
      $signs = array(
        "before" => -1,
        "after"  => +1,
      );
      
      $sign = Cvalue::read($signs, $this->_move);
      
      // Si signe valide et que l'index existe
      if ($sign && isset($groups_ids[$self_index+$sign])) {
        list($groups_ids[$self_index+$sign], $groups_ids[$self_index]) = array($groups_ids[$self_index], $groups_ids[$self_index+$sign]); 
      
        $new_groups = array();
        foreach($groups_ids as $i => $id) {
          $new_groups[$id] = $groups[$id];
        }
        
        $i = 1;
        foreach($new_groups as $_group) {
          if ($_group->_id == $this->_id) {
            $this->rank = $i;
          }
          else {
            $_group->rank = $i;
            $_group->store();
          }
          
          $i++;
        }
      }
    }
    
    return parent::store();
  }
  
  function loadRefsHostFields(){
    return $this->_ref_host_fields = $this->loadBackRefs("host_fields");
  }
  
  function loadRefsMessages(){
    return $this->_ref_messages = $this->loadBackRefs("class_messages");
  }
}
