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

  // DB fields
  var $debut              = null;
  var $fin                = null;
  var $traitement         = null;
  var $dossier_medical_id = null;
  
  function CTraitement() {
    $this->CMbObject("traitement", "traitement_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["debut"       ] = "date";
    $specs["fin"         ] = "date moreEquals|debut";
    $specs["traitement"  ] = "text";
    $specs["dossier_medical_id"] = "notNull ref class|CDossierMedical";
    return $specs;
  }
  
  function loadRefDossierMedical(){ 
    $this->_ref_dossier_medical = new CDossierMedical();
    $this->_ref_dossier_medical->load($this->dossier_medical_id);
  }
  
  

  function getSeeks() {
    return array (
      "traitement" => "like"
    );
  }

  function getHelpedFields(){
    return array(
      "traitement" => null
    );
  }
  
}

?>