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

    static $props = array (
      "object_id"    => "ref|notNull",
      "object_class" => "str|maxLength|25",
      "id400"        => "str|maxLength|8",
      "tag"          => "str|maxLength|80",
      "last_update"  => "dateTime|notNull"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }
  
  function loadRefsFwd() {
    if ($this->object_class) {
    	$this->_ref_object = new $this->object_class;
      $this->_ref_object->load($this->object_id);
    }
  }
  
  /**
   * Binds the id400 to an object, and updates the object
   * Will only bind default object properties when it's created
   */
  function bindObject(&$mbObject, $mbObjectDefault = null) {
    $this->object_class = get_class($mbObject);
    $this->loadMatchingObject("`last_update` DESC");
    $this->loadRefs();
    
    // Object has not been found : never created or deleted since last binding
    if (!$this->_ref_object->_id && $mbObjectDefault) {
      $mbObject->extendsWith($mbObjectDefault);
    }
    
    // Create/update bound object
    $mbObject->_id = $this->object_id;
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
    $this->last_update = mbDate();
  }
}

?>