<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

class CAntecedent extends CMbObject {
  // DB Table key
  var $antecedent_id = null;

  // DB References
  var $object_id    = null;
  var $object_class = null;

  // DB fields
  var $type  = null;
  var $date  = null;
  var $rques = null;
  
  // Object References
  var $_ref_object = null;

  function CAntecedent() {
    $this->CMbObject("antecedent", "antecedent_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "object_id"    => "notNull refMandatory",
      "object_class" => "notNull enum list|CPatient|CConsultAnesth",
      "type"         => "notNull enum list|med|alle|trans|obst|chir|fam|anesth",
      "date"         => "date",
      "rques"        => "text"
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