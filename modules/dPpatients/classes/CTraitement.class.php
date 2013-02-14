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
  var $annule             = null;

  // Form Fields
  var $_search = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'traitement';
    $spec->key   = 'traitement_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["debut"       ] = "date progressive";
    $specs["fin"         ] = "date progressive moreEquals|debut";
    $specs["traitement"  ] = "text helped seekable";
    $specs["dossier_medical_id"] = "ref notNull class|CDossierMedical show|0";
    $specs["annule"] = "bool show|0";

    $specs["_search"] = "str";
    
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->traitement;
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }
  
  function loadRefDossierMedical(){ 
    $this->_ref_dossier_medical = new CDossierMedical();
    $this->_ref_dossier_medical->load($this->dossier_medical_id);
  }
  
  function loadView(){
    parent::loadView();
    $this->loadLogs();
    $this->loadRefDossierMedical();
  }
}

?>