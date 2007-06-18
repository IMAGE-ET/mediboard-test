<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
*/

/**
 * Class CMbMetaObject 
 * @abstract Adds Mediboard abstraction layer functionality for meta-objects
 */

global $AppUI;

global $AppUI;
require_once($AppUI->getSystemClass("mbobject"));

class CMbMetaObject extends CMbObject{
	
  var $object_id    = null;
  var $object_class = null;
	
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["object_id"]    = "notNull ref class|CMbObject meta|object_class";
    $specs["object_class"] = "notNull str maxLength|25";
    return $specs;
  }
    
  
  function loadRefsFwd() {	
    $specs = parent::loadRefsFwd();
  	$this->_ref_object = new $this->object_class;
    if(!$this->_ref_object->load($this->object_id)) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprimé";
    }
  }  
    
}


?>