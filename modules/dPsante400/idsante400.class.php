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