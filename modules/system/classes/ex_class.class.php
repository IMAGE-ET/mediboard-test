<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClass extends CMbObject {
  var $host_class = null;
  var $event      = null;
  
  var $_ref_fields = null;
  var $_ref_constraints = null;
  
  var $_fields_by_name = null;
  var $_host_class_fields = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class";
    $spec->key   = "ex_class_id";
    $spec->uniques["ex_class"] = array("host_class", "event");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["host_class"] = "str notNull protected";
    $props["event"]      = "str notNull protected canonical";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fields"]      = "CExClassField ex_class_id";
    $backProps["constraints"] = "CExClassConstraint ex_class_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = CAppUI::tr($this->host_class) . " - $this->event";
  }
  
  function loadRefsFields(){
    return $this->_ref_fields = $this->loadBackRefs("fields");
  }
  
  function loadRefsConstraints(){
    return $this->_ref_constraints = $this->loadBackRefs("constraints");
  }
  
  function getTableName(){
    $this->completeField("host_class", "event");
    return "ex_{$this->host_class}_{$this->event}";
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
  
  function store(){
    if ($msg = $this->check()) return $msg;
    
    if (!$this->_id) {
      $table_name = $this->getTableName();
      $query = "CREATE TABLE `$table_name` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY) TYPE=MYISAM;";
      
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
    
    $table_name = $this->getTableName();
    $query = "DROP TABLE `$table_name`";
    
    $ds = $this->_spec->ds;
    if (!$ds->query($query)) {
      return "La table '$table_name' n'a pas pu être supprimée (".$ds->error().")";
    }
    
    return parent::delete();
  }
}
