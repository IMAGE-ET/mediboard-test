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

  // Derivate fields
  var $_last_id      = null;
  
  // Object References
  var $_ref_object   = null;

  function CIdSante400() {
    $this->CMbObject("id_sante400", "id_sante400_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "object_class" => "notNull str maxLength|25",
      "object_id"    => "notNull ref",
      "id400"        => "notNull str maxLength|10",
      "tag"          => "str maxLength|80",
      "last_update"  => "notNull dateTime"
    );
  }
  
  function loadRefsFwd() {
    if ($this->object_class) {
    	$this->_ref_object = new $this->object_class;
      $this->_ref_object->load($this->object_id);
    }
  }
  
  /**
   * Loads a specific id400 for a given object (and optionnaly tag)
   */
  function loadLatestFor($mbObject, $tag = null) {
    $object_class = get_class($mbObject);
    if (!is_a($mbObject, "CMbObject")) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }
    
    $this->_id = null;
    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
    $this->tag = $tag;
    $this->loadMatchingObject("`last_update` DESC");
  }
  
  /**
   * Tries to get an already bound object if id400 is not older than delay
   */
  function getCachedObject($delay) {
    $this->_id = null;
    $this->loadMatchingObject("`last_update` DESC");
    $this->loadRefsFwd();

    if (mbDateTime($delay, $this->last_update) < mbDateTime()) {
      $this->_ref_object = new $this->object_class;
    }

    return $this->_ref_object;
  }
  
  /**
   * Binds the id400 to an object, and updates the object
   * Will only bind default object properties when it's created
   */
  function bindObject(&$mbObject, $mbObjectDefault = null) {
    $object_class = get_class($mbObject);
    if (!is_a($mbObject, "CMbObject")) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }

    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
    $this->loadMatchingObject("`last_update` DESC");
    $this->loadRefs();
    
    // Object has not been found : never created or deleted since last binding
    if (!@$this->_ref_object->_id && $mbObjectDefault) {
      $mbObject->extendsWith($mbObjectDefault);
    }
    
    // Create/update bound object
    $mbObject->_id = $this->object_id;
    $mbObject->repair();
    if ($msg = $mbObject->store()) {
      throw new Exception($msg);
    }
    
    $this->object_id = $mbObject->_id;
    $this->last_update = mbDateTime();

    // Create/update the idSante400    
    if ($msg = $this->store()) {
      throw new Exception($msg);
    }
  }
  
  function setObject($mbObject) {
    $object_class = get_class($mbObject);
    if (!is_a($mbObject, "CMbObject")) {
      trigger_error("Impossible d'associer un identifiant Santé 400 à un objet de classe '$object_class'");
    }
    
    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
  }
}

?>