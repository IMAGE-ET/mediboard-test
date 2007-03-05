<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

class CAddiction extends CMbObject {
  // DB Table key
  var $addiction_id = null;

  // DB References
  var $object_id    = null;
  var $object_class = null;

  // DB fields
  var $type      = null;
  var $addiction = null;
  
  // Object References
  var $_ref_object = null;

  function CAddiction() {
    $this->CMbObject("addiction", "addiction_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "object_id"    => "notNull ref",
      "object_class" => "notNull enum list|CConsultAnesth",
      "type"         => "notNull enum list|tabac|oenolisme|cannabis",
      "addiction"    => "text"
    );
  }
  
  function loadRefsFwd() {
    // Objet
    if (class_exists($this->object_class)) {
      $this->_ref_object = new $this->object_class;
      if ($this->object_id)
        $this->_ref_object->load($this->object_id);
    } else {
      trigger_error("Enable to create instance of '$this->object_class' class", E_USER_ERROR);
    }
  }
}
?>