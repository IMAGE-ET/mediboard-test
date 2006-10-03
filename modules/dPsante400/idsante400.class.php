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
  function bindObject(&$mbObject, $id400, $mbObjectDefault = null, $tag = null) {
    $this->object_class = get_class($mbObject);
    $this->id400 = $id400;
    $this->tag = $tag;
    $this->loadMatchingObject("`last_update` DESC");
    $this->last_update = mbDateTime();
    
    //-- Object not found
    if (!$this->_id) {
      if ($mbObjectDefault) {
        foreach ($mbObjectDefault->getProps() as $propName => $propValue) {
          $mbObject->$propName = $propValue;
        }
      }
      
      // Create object
      if ($msg = $mbObject->store()) {
        throw new Exception($msg);
      }
      
      // Create IdSante400
      $this->object_id = $mbObject->_id;
      if ($msg = $this->store()) {
        throw new Exception($msg);
      }
      
      return;
    }
    
    // !!!! Use default when recreated
    //-- Object Found
    // Update object
    $mbObject->_id = $this->object_id;
    
    mbTrace($mbObject->_id, "ID to store for " . get_class($mbObject));
    if ($msg = $mbObject->store()) {
      throw new Exception($msg);
    }
    
    // Update IdSante400 
    $this->object_id = $mbObject->_id; // Object might have been re-recreated
    if ($msg = $this->store()) {
      throw new Exception($msg);
    }
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