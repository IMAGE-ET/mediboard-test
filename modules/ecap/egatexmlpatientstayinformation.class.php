<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CEGateXMLPatientStayInformation extends CEGateXMLDocument {
  function __construct() {
    parent::__construct("patientstayinformation");
    $patientStayInfo = $this->addElement($this, "PatientStayInformation", null, "http://www.capio.com");
    
    $header = $this->addElement($patientStayInfo, "Header");
    $this->addAttribute($header, "Created"     , mbTransformTime(null, null, "%Y-%m-%dT%H:%M:%S"));
    $this->addAttribute($header, "Originator"  , "Mediboard");
    $this->addAttribute($header, "CountryCode" , "FR");
  }
  
  function generateFromSejour($mbSejour) {
    $patientStayInfo = $this->documentElement;
    
    $patientStay = $this->addElement($patientStayInfo, "PatientStay");
    $this->addAttribute($patientStay, "PatientStayID" , $mbSejour->sejour_id);
    $this->addAttribute($patientStay, "MainUnitID"    , $mbSejour->group_id);
    if($mbSejour->type == "comp" || $mbSejour->type == "ssr" || $mbSejour->type == "psy"){
      $this->addAttribute($patientStay, "BA_TypeOfStayID" , "1");
    }elseif($mbSejour->type == "ambu" || $mbSejour->type == "seances"){
      $this->addAttribute($patientStay, "BA_TypeOfStayID" , "2");
    }elseif($mbSejour->type == "exte"){
      $this->addAttribute($patientStay, "BA_TypeOfStayID" , "3");
    }
    
    // Patient
    $mbPatient =& $mbSejour->_ref_patient;
    $patient = $this->addElement($patientStay, "Patient");
    $this->addAttribute($patient, "PatientID"   , $mbPatient->patient_id);
    $this->addAttribute($patient, "DateOfBirth" , $mbPatient->naissance."T00:00:00");
    $this->addAttribute($patient, "Firstname"   , $mbPatient->nom);
    $this->addAttribute($patient, "Lastname"    , $mbPatient->prenom);
    
    if($mbPatient->naissance == "0000-00-00"){
      $this->msgError[] = "La date de naissance du patient n'est pas renseigné.";
    }
    
    if($mbPatient->sexe && $mbPatient->sexe == "m"){
      $this->addAttribute($patient, "Gender"    , "M");
    }elseif($mbPatient->sexe){
      $this->addAttribute($patient, "Gender"    , "F");
    }
    if($mbPatient->tel){
      $this->addAttribute($patient, "Phone1"    , $mbPatient->tel);
    }
    if($mbPatient->tel2){
      $this->addAttribute($patient, "Phone2"    , $mbPatient->tel2);
    }
    $this->addAttribute($patient, "AcceptCall"  , "0");
    
    // StayTime
    $mbSejourDebut = mbGetValue(
      $mbSejour->entree_reelle, 
      $mbSejour->entree_prevue
    );
    $mbSejourFin = mbGetValue(
      $mbSejour->sortie_reelle, 
      $mbSejour->sortie_prevue
    );
    $stayTime = $this->addElement($patientStay, "StayTime");
    $this->addAttribute($stayTime, "TypeID" , "1");
    $this->addDateTimeElement($stayTime, "Start", $mbSejourDebut);
    $this->addDateTimeElement($stayTime, "End"  , $mbSejourFin);
    
    //Affectations
    if(count($mbSejour->_ref_affectations)){
      $this->addAffectations($patientStay, $mbSejour);
    }
    
    // Anesthesie
    if(count($mbSejour->_ref_operations)){
      $this->addOperations($patientStay, $mbSejour);
    }    
    
    
    //Main Diagnosis
    if($mbSejour->DP){
      $mainDiagnosis = $this->addElement($patientStay, "MainDiagnosis");
      $this->addAttribute($mainDiagnosis, "DiagnosisCode" , $mbSejour->DP);
      $this->addAttribute($mainDiagnosis, "Version"       , "10");
    }
    
    
    $this->addElement($patientStayInfo, "Checksum", "1");
    
    // Traitement final
    $this->purgeEmptyElements();
  }
}

?>
