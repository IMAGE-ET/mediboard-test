<?php /* $Id: plageressource.class.php 703 2006-09-01 16:21:47Z maskas $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 703 $
* @author Romain Ollivier
*/

/**
 * Stores id linkage between Mediboard and Sante400 records
 */
 class CIdSante400 extends CMbObject {
  // DB Table key
  var $id_sante400_id = null;

  // DB References
  var $object_id     = null;
  var $object_class  = null;

  // DB fields
  var $id400         = null;
  var $tag           = null;
  var $last_update   = null;

  // Object References
  var $_ref_object   = null;

  function CIdSante400() {
    $this->CMbObject("id_sante400", "id_sante400_id");
    
    $this->_props["object_id"]    = "ref|notNull";
    $this->_props["object_class"] = "str|maxLength|25";
    $this->_props["tag"]          = "str|maxLength|80";
    $this->_props["last_update"]  = "dateTime|notNull";
  }
  
  function loadRefsFwd() {
    if ($this->object_class) {
    	$this->_ref_object = new $this->object_class;
      $this->_ref_object->load($this->object_id);
    }
  }
  
  /**
   * Binds the id400 to an object, and updates the object
   */
  function bindObject(&$mbObject, $id400, $tag = null) {
    $this->object_class = get_class($mbObject);
    $this->id400 = $id400;
    $this->tag = $tag;
    $this->loadMatchingObject("`last_update` DESC");
    $this->last_update = mbDateTime();
    
    mbTrace($this, "ID400 before binding");
    mbTrace($mbObject, "Object before binding");

    // Object not found
    if (!$this->_id) {
      // Create object
      if ($msg = $mbObject->store()) {
        throw new Exception($msg);
      }
      mbTrace($mbObject, "Object after creating");
      
      // Create IdSante400
      $this->object_id = $mbObject->_id;
      if ($msg = $this->store()) {
        throw new Exception($msg);
      }
      mbTrace($mbObject, "Object after creating");
      
      return;
    }
    
    // Object Found
    // Update object
    $mbObject->_id = $this->object_id;
    if ($msg = $mbObject->store()) {
      throw new Exception($msg);
    }
    mbTrace($mbObject, "Object after updating");
    
    // Update IdSante400
    if ($msg = $this->store()) {
      throw new Exception($msg);
    }
    mbTrace($this, "ID400 after updating");
  }
  
  function setIdentifier($mbObject, $id400, $tag = null) {
    $object_class = get_class($mbObject);
    if (!is_a($mbObject, "CMbObject")) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }
    
    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
    $this->tag = $tag;
    $this->last_update = mbDate();
  }
}

?>