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
  
  /**
   * @var CExClass
   */
  private $_ref_ex_class = null;
  private $_host_class = null;

  function setExClass(CExClass $exClass) {
    if ($this->_ref_ex_class) return;
    
    $this->_ref_ex_class = $exClass;
    $this->_host_class = $exClass->host_class;
    $this->_spec->table = $exClass->getTableName();
    $this->_spec->key = "id";
    
    $this->_props = $this->getProps();
    $this->_specs = $this->getSpecs();
    
    $this->_class_name .= "_{$exClass->host_class}_{$exClass->event}";
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    if ($this->_ref_ex_class) {
      //$this->_ref_ex_class->loadRefsFields(); // do not load the fields or we won't be able to change the SQL spec
      $fields = $this->_ref_ex_class->_ref_fields;
      foreach($fields as $_field) {
        $this->{$_field->name} = null; // declaration of the field
        $specs[$_field->name] = $_field->prop; // declaration of the field spec
      }
    }
    
    return $specs;
  }
}
