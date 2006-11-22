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
  }

  function getSpecs() {
    return array (
      "patient_id" => "ref|notNull",
      "debut"      => "date",
      "fin"        => "date|moreEquals|debut",
      "traitement" => "text"
    );
  }
  
  function getSeeks() {
    return array (
      "traitement" => "like"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
}

?>