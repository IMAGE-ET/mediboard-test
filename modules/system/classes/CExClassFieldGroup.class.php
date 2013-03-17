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

class CExClassFieldGroup extends CMbObject {
  public $ex_class_field_group_id;
  
  public $ex_class_id;
  public $name; // != object_class, object_id, ex_ClassName_event_id,
  public $rank;

  /** @var CExClass */
  public $_ref_ex_class;

  /** @var CExClassField[] */
  public $_ref_fields;

  /** @var CExClassMessage[] */
  public $_ref_messages;

  /** @var CExClassHostField[] */
  public $_ref_host_fields;

  /** @var CExClassFieldSubgroup[] */
  public $_ref_subgroups;

  /** @var CExClassField[] */
  public $_ref_root_fields;

  /** @var CExClassMessage[] */
  public $_ref_root_messages;
  
  public $_move;

  /** @var CExClassField[] */
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
    $backProps["class_fields"]   = "CExClassField ex_group_id";
    $backProps["host_fields"]    = "CExClassHostField ex_group_id";
    $backProps["class_messages"] = "CExClassMessage ex_group_id";
    $backProps["subgroups"]      = "CExClassFieldSubgroup parent_id";
    $backProps["identifiants"]   = "CIdSante400 object_id cascade";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  /**
   * @param bool $cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }

  /**
   * @return CExClassFieldGroup
   */
  function getExGroup(){
    return $this;
  }

  /**
   * @param bool $cache
   *
   * @return CExClassField[]
   */
  function loadRefsRootFields($cache = true){
    $fields = $this->loadRefsFields($cache);

    foreach ($fields as $_id => $_field) {
      if ($_field->subgroup_id) {
        unset($fields[$_id]);
      }
    }

    return $this->_ref_root_fields = $fields;
  }

  /**
   * @param bool $cache
   *
   * @return CExClassMessage[]
   */
  function loadRefsRootMessages($cache = true){
    $messages = $this->loadRefsMessages($cache);

    foreach ($messages as $_id => $_message) {
      if ($_message->subgroup_id) {
        unset($messages[$_id]);
      }
    }

    return $this->_ref_root_messages = $messages;
  }

  /**
   * @param bool $cache
   *
   * @return CExClassField[]
   */
  function loadRefsFields($cache = true){
    if ($cache && isset(self::$_fields_cache[$this->_id])) {
      return $this->_ref_fields = self::$_fields_cache[$this->_id];
    }

    $this->_ref_fields = $this->loadBackRefs("class_fields", "IF(tab_index IS NULL, 10000, tab_index), ex_class_field_id");
    
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
        foreach ($groups_ids as $id) {
          $new_groups[$id] = $groups[$id];
        }
        
        $i = 1;
        foreach ($new_groups as $_group) {
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

  /**
   * @return CExClassHostField[]
   */
  function loadRefsHostFields(){
    return $this->_ref_host_fields = $this->loadBackRefs("host_fields");
  }

  /**
   * @param bool $cache
   *
   * @return CExClassMessage[]
   */
  function loadRefsMessages($cache = true){
    if ($cache && $this->_ref_messages) {
      return $this->_ref_messages;
    }

    return $this->_ref_messages = $this->loadBackRefs("class_messages");
  }

  /**
   * @param bool $recurse
   *
   * @return CExClassFieldSubgroup[]
   */
  function loadRefsSubgroups($recurse = false){
    $this->_ref_subgroups = $this->loadBackRefs("subgroups", "ex_class_field_subgroup_id");

    if ($recurse) {
      foreach ($this->_ref_subgroups as $_subgroup) {
        $_subgroup->loadRefsChildrengroups($recurse);
      }
    }

    return $this->_ref_subgroups;
  }
}
