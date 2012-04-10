<?php

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbMetaObject extends CMbObject {
  var $object_id    = null;
  var $object_class = null;

  /**
   * @var CMbObject
   */
  var $_ref_object  = null;
  
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]    = "ref notNull class|CMbObject meta|object_class";
    $specs["object_class"] = "str notNull class show|0";
    return $specs;
  }
    
  function setObject(CMbObject $object) {
    $this->_ref_object  = $object;
    $this->object_id    = $object->_id;
    $this->object_class = $object->_class;
  }
  
  function loadListFor(CMbObject $object) {
    $this->setObject($object);
    return $this->loadMatchingList();
  }
  
  /**
   * Load target of meta object
   * 
   * @return CMbObject
   */
  function loadTargetObject($cache = true) {
    if ($this->_ref_object || !$this->object_class) {
      return $this->_ref_object;
    }
    
    if (!class_exists($this->object_class)) {
      $ex_object = CExObject::getValidObject($this->object_class);
      
      if (!$ex_object) {
        trigger_error("Unable to create instance of '$this->object_class' class", E_USER_ERROR);
        return;
      }
      else {
        $ex_object->load($this->object_id);
        $this->_ref_object = $ex_object;
      }
    }

    else {
      $this->_ref_object = $this->loadFwdRef("object_id", $cache);
    }
    
    if (!$this->_ref_object->_id) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprimé";
    }
    
    return $this->_ref_object;
  }
    
  function loadRefsFwd() {  
    parent::loadRefsFwd();
    $this->loadTargetObject();
  }
}
