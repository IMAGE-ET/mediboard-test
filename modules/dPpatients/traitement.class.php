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

    $this->_props["patient_id"] = "ref|notNull";
    $this->_props["debut"]      = "date|notNull";
    $this->_props["fin"]        = "date|moreEquals|debut";
    $this->_props["traitement"] = "text";
    
    $this->_seek["traitement"] = "like";
  }
  
  function loadRefsFwd() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
}

?>