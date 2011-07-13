<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

CAppUI::requireModuleClass("dPcabinet", "lmObject");

/**
 * FSE produite par LogicMax
 */
class CLmFSE extends CLmObject {  
  // DB Table key
  var $S_FSE_NUMERO_FSE = null;
  
  var $_annulee = null;

  // DB Fields : see getProps();

  // Filter Fields
  var $_date_min = null;
  var $_date_max = null;
  
  // References
  var $_ref_id = null;
  var $_ref_lot = null;
  
  // Distant field
  var $_consult_id = null;

	function updateFormFields() {
	  parent::updateFormFields();
	  $this->_annulee = $this->S_FSE_ETAT == "3";
	}
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CConsultation';
    $spec->table   = 'S_F_FSE';
    $spec->key     = 'S_FSE_NUMERO_FSE';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    
    // DB Fields
    $specs["S_FSE_NUMERO_FSE"]        = "ref class|CLmFSE";
    $specs["S_FSE_ETAT"]              = "enum list|1|2|3|4|5|6|7|8|9|10";
    $specs["S_FSE_MODE_SECURISATION"] = "enum list|0|1|2|3|4|5|255";
    $specs["S_FSE_DATE_FSE"]          = "date";
    $specs["S_FSE_CPS"]               = "num";
    $specs["S_FSE_VIT"]               = "num";
    $specs["S_FSE_LOT"]               = "ref class|CLmLot";
    $specs["S_FSE_TOTAL_FACTURE"]     = "currency";
    $specs["S_FSE_TOTAL_AMO"]         = "currency";
    $specs["S_FSE_TOTAL_ASSURE"]      = "currency";
    $specs["S_FSE_TOTAL_AMC"]         = "currency";
    //infos supplémentaires sur le patient
    $specs["S_FSE_IMM_NUM"]           = "num";
    $specs["S_FSE_IMM_CLE"]           = "num";
    $specs["S_FSE_DATE_NAISSANCE"]    = "num";
    //infos supplémentaires sur le praticien
    $specs["S_FSE_CPS_NOM"]           = "text";

    // Filter Fields
    $specs["_date_min"] = "date";
    $specs["_date_max"] = "date moreThan|_date_min";
    
    // Distant field
    $specs["_consult_id"] = "ref class|CConsultation";
    
    return $specs;
  }
  
  function loadRefLot() {
    $lot = new CLmLot();
    $this->_ref_lot = $lot->getCached($this->S_FSE_LOT);
  }
  
  function loadRefIdExterne() {
    $this->_ref_id = new CIdSante400();
    $this->_ref_id->object_class = "CConsultation";
    $this->_ref_id->tag = "LogicMax FSENumero";
    $this->_ref_id->id400 = $this->_id;
    $this->_ref_id->loadMatchingObject();
    
    $this->_consult_id = $this->_ref_id->object_id;
  }
  
  function detectlink(){
    $this->loadRefIdExterne();
    //chargement de la consultation associée à la FSE
    $consult = new CConsultation();
    $is_consult = $consult->load($this->_consult_id);
    
    //chargement de la plage de consultation
    $plage   = new CPlageconsult();
    $plage->load($consult->plageconsult_id);
    
    //chargement du patient associée à la consultation
    $patient = new CPatient();
    $patient->load($consult->patient_id);
    $patient->loadIdVitale();
    
    //chargement du praticien qui a fait la consultation
    $mediuser = new CMediusers();
    $mediuser->load($consult->getExecutantId());
    $mediuser->loadIdCPS();
  
    /*vérification des champs de chaque coté
     * les données sur le praticien, le patient, la date de consultation doivent corespondre avec la FSE
     */
    $check_prat      = ($this->S_FSE_CPS == $mediuser->_id_cps);
    
    $check_dateFSE   = ($this->S_FSE_DATE_FSE == $plage->date);
    
    $check_patient   = ($this->S_FSE_VIT == $patient->_id_vitale); 
    
    $msg = array();   
    if (!$check_prat | !$check_patient | !$check_dateFSE){
      if (!$is_consult){
        $msg[]= " FSE non associée";
      }
      else{
        if  (!$check_prat && !$check_patient && !$check_dateFSE){
          $msg[]= " FSE mal associée";
        }
        else{
          if (!$check_prat){
            $msg[]= " FSE mal associée au bon praticien";
          }
          if (!$check_dateFSE){
            $msg[]= " FSE mal associée à la bonne date";
          }         
          if (!$check_patient){
            $msg[]= " FSE mal associée au bon patient";
          }
        }
      }
    }
    return $msg;
  }
}

?>