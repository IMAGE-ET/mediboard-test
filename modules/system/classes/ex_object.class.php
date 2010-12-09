<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExObject extends CMbMetaObject {
  var $id = null;
  
  var $_ex_class_id = null;
  
  /**
   * @var CExClass
   */
  public $_ref_ex_class = null;

  function setExClass() {
    //if ($this->_spec->key) return;
    
    if (!$this->_ex_class_id) return;
    
    $ex_class = $this->loadRefExClass();
    
    $this->_ref_ex_class = $ex_class;
    
    $this->_props = $this->getProps();
    $this->_specs = $this->getSpecs();
    
    $this->_class_name = get_class($this)."_{$ex_class->host_class}_{$ex_class->event}";
  }
  
  function loadRefExClass($cache = true){
    if ($cache && $this->_ref_ex_class && $this->_ref_ex_class->_id) return $this->_ref_ex_class;
    
    $ex_class = new CExClass();
    $ex_class->load($this->_ex_class_id);
    
    return $this->_ref_ex_class = $ex_class; // can't use loadFwdRef here
  }
  
  function bind($hash, $doStripSlashes = true) {
    $this->loadRefExClass();
    $this->setExClass();
    
    return parent::bind($hash, $doStripSlashes);
  }
  
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new self;
      $this->_old->_ex_class_id = $this->_ex_class_id;
      $this->_old->setExClass();
      $this->_old->load($this->_id);
    }
  }
  
  function getDBFields() {
    $this->loadRefExClass();
    $this->setExClass();
    
    return parent::getDBFields();
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key   = "id";
    return $spec;
  }
  
  // needed or will throw errors in the field specs
  function checkProperty($propName) {
    $class_name = $this->_class_name;
    $this->_class_name = get_class($this);
    
    $spec = $this->_specs[$propName];
    $ret = $spec->checkPropertyValue($this);
    
    $this->_class_name = $class_name;
    return $ret;
  }
  
  function getProps() {
    $ex_class = $this->loadRefExClass();
    
    $this->_spec->table = $ex_class->getTableName();
    
    $props = parent::getProps();
    $props["_ex_class_id"] = "ref class|CExClass";
    
    $fields = $this->_ref_ex_class->loadRefsFields();
    
    foreach($fields as $_field) {
      if (isset($this->{$_field->name})) break; // don't redeclare them more than once
      $this->{$_field->name} = null; // declaration of the field
      $props[$_field->name] = $_field->prop; // declaration of the field spec
    }
    
    return $props;
  }
  
  function getSpecs(){
    $ex_class = $this->loadRefExClass();
    $this->_class_name = get_class($this)."_{$ex_class->host_class}_{$ex_class->event}";
    
    $specs = parent::getSpecs();
        
    foreach($specs as $_field => $_spec) {
      if ($_spec instanceof CEnumSpec) {
        foreach ($_spec->_locales as $key => $locale) {
          $specs[$_field]->_locales[$key] = CAppUI::tr("$this->_class_name.$_field.$key");
        }
      }
    }
    
    return $specs;
  }
}
