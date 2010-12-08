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
  private $_host_class = null;

  function setExClass(CExClass $ex_class = null) {
    if ($this->_spec->key) return;
    
    if (!$ex_class) {
      $ex_class = $this->loadRefExClass();
      if (!$ex_class->_id) return;
    }
    
    $this->_ex_class_id = $ex_class->_id;
    $this->_ref_ex_class = $ex_class;
    
    $this->_props = $this->getProps();
    $this->_specs = $this->getSpecs();
    
    $this->_class_name .= "_{$ex_class->host_class}_{$ex_class->event}";
  }
  
  function loadRefExClass($cache = true){
    if ($cache && $this->_ref_ex_class) return $this->_ref_ex_class;
    
    $ex_class = new CExClass();
    $ex_class->load($this->_ex_class_id);
    
    return $this->_ref_ex_class = $ex_class; // can't use loadFwdRef here
  }
  
  function bind($hash, $doStripSlashes = true) {
    $this->setExClass();
    return parent::bind($hash, $doStripSlashes);
  }
  
  function getDBFields() {
    $result = array();
    
    $this->setExClass();
    $fields = $this->_ref_ex_class->loadRefsFields();
    
    $vars = get_object_vars($this);
    foreach($fields as $_field) {
      $vars[$_field->name] = $this->{$_field->name};
    }
    
    foreach($vars as $key => $value) {
      if ($key[0] !== '_') {
        $result[$key] = $value;
      }
    }

    return $result;
  }
  
  function getProps() {
    $ex_class = $this->loadRefExClass();
    
    $this->_host_class = $ex_class->host_class;
    
    $this->_spec->table = $ex_class->getTableName();
    $this->_spec->key = "id";
    
    $specs = parent::getProps();
    $specs["_ex_class_id"] = "ref class|CExClass";
    
    $fields = $this->_ref_ex_class->loadRefsFields();
    
    foreach($fields as $_field) {
      $this->{$_field->name} = null; // declaration of the field
      $specs[$_field->name] = $_field->prop; // declaration of the field spec
    }
    
    return $specs;
  }
}
