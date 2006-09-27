<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

class CTraitement extends CMbObject {
  // DB Table key
  var $traitement_id = null;

  // DB References
  var $patient_id = null;

  // DB fields
  var $debut      = null;
  var $fin        = null;
  var $traitement = null;
  
  // Object References
  var $_ref_patient = null;

  function CTraitement() {
    $this->CMbObject("traitement", "traitement_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "patient_id" => "ref|notNull",
      "debut"      => "date|notNull",
      "fin"        => "date|moreEquals|debut",
      "traitement" => "text"
    );
    $this->_props =& $props;

    static $seek = array (
      "traitement" => "like"
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
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
}

?>