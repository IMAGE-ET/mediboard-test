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

  // DB fields
  var $type               = null;
  var $appareil           = null;
  var $date               = null;
  var $rques              = null;
  var $dossier_medical_id = null;
  var $annule             = null;
  
  // Form Fields
  var $_search = null;
  
  static $types = array(
	  'med', 'alle', 'trans', 'obst', 
	  'chir', 'fam', 'anesth', 'gyn', 
	  'cardio', 'pulm', 'stomato', 'plast', 
	  'ophtalmo', 'digestif', 'gastro', 
	  'stomie', 'uro', 'ortho', 'traumato', 'amput',
	  'neurochir', 'greffe', 'thrombo', 'cutane',
	  'hemato', 'rhumato', 'neuropsy', 'infect',
	  'endocrino', 'carcino', 'orl', 'addiction', 'habitus'
	);
	  
	static $appareils = array(
	  'cardiovasculaire', 'digestif', 'endocrinien', 'neuro_psychiatrique',
	  'pulmonaire', 'uro_nephrologique', 'orl', 'gyneco_obstetrique', 'orthopedique'
	);
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'antecedent';
    $spec->key   = 'antecedent_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["type"]  = "enum list|".CAppUI::conf("dPpatients CAntecedent types");
    $specs["appareil"] = "enum list|".CAppUI::conf("dPpatients CAntecedent appareils");
    $specs["date"]  = "date";
    $specs["rques"] = "text";
    $specs["dossier_medical_id"] = "ref class|CDossierMedical";
    $specs["annule"] = "bool";
    $specs["_search"] = "str";
    return $specs;
  }
  
  function loadRefDossierMedical(){ 
    $this->_ref_dossier_medical = new CDossierMedical();
    $this->_ref_dossier_medical->load($this->dossier_medical_id);
  }
  
  function loadView(){
    $this->loadLogs();
    $this->loadRefDossierMedical();
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    // DossierMedical store
    $this->checkCodeCim10();
  }
  
  function checkCodeCim10(){
    preg_match_all("/[A-Z]\d{2}\.?\d{0,2}/i", $this->rques, $matches);
    
    foreach($matches as $key => $match_){
      foreach($match_ as &$match){
        // Transformation du code CIM pour le tester
        $match = str_replace(".","",$match);
        $match = strtoupper($match);
        
        // Chargement du code CIM 10
        $code_cim10 = new CCodeCIM10($match, 1);
    
        if($code_cim10->libelle != "Code CIM inexistant"){
          // Cas du code valide, sauvegarde du code CIM
          $dossier_medical = new CDossierMedical();
          $dossier_medical->load($this->dossier_medical_id);
          
          // si le code n'est pas deja present, on le rajoute
          if(!array_key_exists($match, $dossier_medical->_ext_codes_cim)){
            if($dossier_medical->codes_cim != ""){
              $dossier_medical->codes_cim .= "|";
            }
            $dossier_medical->codes_cim .= $match;
            $dossier_medical->store();
          }
        }
      }
    }
  }
  
  function getHelpedFields(){
    //type => depend_value_1
    //appareil => depend_value_2
    return array(
      "rques" => array("depend_value_1" => "type", "depend_value_2" => "appareil")
    ); 
  }
}

?>