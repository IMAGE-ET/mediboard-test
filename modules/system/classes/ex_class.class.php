<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClass extends CMbObject {
  var $ex_class_id = null;
  
  var $host_class = null;
  var $event      = null;
  var $name       = null;
  
  var $_ref_fields = null;
  var $_ref_constraints = null;
  
  var $_fields_by_name = null;
  var $_host_class_fields = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class";
    $spec->key   = "ex_class_id";
    $spec->uniques["ex_class"] = array("host_class", "event", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["host_class"] = "str notNull protected";
    $props["event"]      = "str notNull protected canonical";
    $props["name"]       = "str notNull";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fields"]      = "CExClassField ex_class_id";
    $backProps["constraints"] = "CExClassConstraint ex_class_id";
    return $backProps;
  }
  
  function load($id = null) {
    if (!($ret = parent::load($id))) {
      return $ret;
    }
    
    // pas encore obligé d'utiliser l'eval, mais je pense que ca sera le plus simple
    /*$class_name = "CExObject_{$this->host_class}_{$this->event}_{$this->_id}";
    
    if (!class_exists($class_name)) {
      $table_name = $this->getTableName();
      
      eval("
      class $class_name extends CExObject {
        function getSpec(){
          \$spec = parent::getSpec();
          \$spec->table = '$table_name';
          return \$spec;
        }
      }
      ");
    }*/
    
    return $ret;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = CAppUI::tr($this->host_class) . " - [$this->event] - $this->name";
  }
  
  function loadRefsFields(){
    if (!empty($this->_ref_fields)) return $this->_ref_fields;
    return $this->_ref_fields = $this->loadBackRefs("fields");
  }
  
  function loadRefsConstraints(){
    return $this->_ref_constraints = $this->loadBackRefs("constraints");
  }
  
  function getTableName(){
    $this->completeField("host_class", "event");
    return strtolower("ex_{$this->host_class}_{$this->event}_{$this->_id}");
  }
  
  function checkConstraints(CMbObject $object){
    $constraints = $this->loadRefsConstraints();
    
    foreach($constraints as $_constraint) {
      if (!$_constraint->checkConstraint($object)) return false;
    }
    
    return true;
  }
  
  function getAvailableFields(){
    $object = new $this->host_class;
    return $this->_host_class_fields = array_keys($object->getDBFields());
  }
  
  function loadExObjects(CMbObject $object) {
    $ex_object = new CExObject;
    $ex_object->_ex_class_id = $this->_id;
    $ex_object->loadRefExClass();
    $ex_object->setExClass();
    $ex_object->setObject($object);
    return $ex_object->loadMatchingList();
  }
  
  function store(){
    if ($msg = $this->check()) return $msg;
    
    if (!$this->_id) {
      if ($msg = parent::store()) {
        return $msg;
      }
      
      $table_name = $this->getTableName();
      $query = "CREATE TABLE `$table_name` (
        `ex_object_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `object_id` INT UNSIGNED NOT NULL,
        `object_class` VARCHAR(80) NOT NULL,
        INDEX ( `object_id` ),
        INDEX ( `object_class` )
      ) TYPE=MYISAM;";
      
      $ds = $this->_spec->ds;
      if (!$ds->query($query)) {
        return "La table '$table_name' n'a pas pu être créée (".$ds->error().")";
      }
    }
    
    else if ($this->fieldModified("event")) {
      $table_name_old = $this->_old->getTableName();
      $table_name     = $this->getTableName();
      $query = "ALTER TABLE `$table_name_old` RENAME `$table_name`";
      
      $ds = $this->_spec->ds;
      if (!$ds->query($query)) {
        return "La table '$table_name' n'a pas pu être renommée (".$ds->error().")";
      }
    }
    
    return parent::store();
  }
  
  function delete(){
    if ($msg = $this->canDeleteEx()) return $msg;
    
    // suppression des objets des champs sans supprimer les colonnes de la table
    $fields = $this->loadBackRefs("fields");
    foreach($fields as $_field) {
      $_field->_dont_drop_column = true;
      $_field->delete();
    }
    
    $table_name = $this->getTableName();
    $query = "DROP TABLE `$table_name`";
    
    $ds = $this->_spec->ds;
    if (!$ds->query($query)) {
      return "La table '$table_name' n'a pas pu être supprimée (".$ds->error().")";
    }
    
    return parent::delete();
  }
}
