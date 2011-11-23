<?php

/**
 * Admit Discharge Transfer HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventADT 
 * Admit Discharge Transfer
 */
class CHL7v2EventADT extends CHL7v2Event implements CHL7EventADT {
  function __construct($i18n = null) {
    parent::__construct($i18n);
    
    $this->profil      = $i18n ? "PAM_$i18n" : "PAM";
    $this->event_type  = "ADT";
  }
  
  function build($object) {
    parent::build($object);
        
    // Message Header 
    $this->addMSH();
    
    // Event Type
    $this->addEVN();
  }
  
  /*
   * MSH - Represents an HL7 MSH message segment (Message Header) 
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }
  
  /*
   * Represents an HL7 EVN message segment (Event Type)
   */
  function addEVN() {
    $EVN = CHL7v2Segment::create("EVN", $this->message);
    $EVN->planned_datetime = null;
    $EVN->occured_datetime = null;
    $EVN->build($this);
  }
  
  /*
   * Represents an HL7 PID message segment (Patient Identification)
   */
  function addPID(CPatient $patient) {
    $PID = CHL7v2Segment::create("PID", $this->message);
    $PID->patient = $patient;
    $PID->set_id  = 1;
    $PID->build($this);
  }
  
  /*
   * Represents an HL7 PD1 message segment (Patient Additional Demographic)
   */
  function addPD1(CPatient $patient) {
    $PD1 = CHL7v2Segment::create("PD1", $this->message);
    $PD1->patient = $patient;
    $PD1->build($this);
  }
  
  /*
   * Represents an HL7 ROL message segment (Role)
   */
  function addROLs(CPatient $patient) {
    $patient->loadRefsCorrespondants();
    if ($patient->_ref_medecin_traitant->_id) {
      $ROL = CHL7v2Segment::create("ROL", $this->message);
      $ROL->medecin = $patient->_ref_medecin_traitant;
      $ROL->role_id = "ODRP";
      // Mise à jour du médecin
      if ($patient->fieldModified("medecin_traitant")) {
        $ROL->action = "UP";
      }
      $ROL->build($this);
    }
    
    foreach ($patient->_ref_medecins_correspondants as $_correspondant) {
      $medecin = $_correspondant->loadRefMedecin();
      if ($medecin->type != "medecin") {
        continue;
      }
      $ROL = CHL7v2Segment::create("ROL", $this->message);
      $ROL->medecin = $medecin;
      $ROL->role_id = "RT";
      $ROL->build($this);
    }
  }
  
  /*
   * Represents an HL7 NK1 message segment (Next of Kin / Associated Parties)
   */
  function addNK1s(CPatient $patient) {
    $i = 1;
    foreach ($patient->loadRefsCorrespondantsPatient() as $_correspondant) {
      $NK1 = CHL7v2Segment::create("NK1", $this->message);
      $NK1->set_id = $i;
      $NK1->correspondant = $_correspondant;
      $NK1->build($this);
      $i++;
    }
  }
  
  /*
   * Represents an HL7 PV1 message segment (Patient Visit)
   */
  function addPV1(CSejour $sejour = null, $set_id = 1) {
    $PV1 = CHL7v2Segment::create("PV1", $this->message);
    $PV1->sejour = $sejour;
    $PV1->set_id = 1;
    $PV1->build($this);
  }
  
  /*
   * Represents an HL7 PV2 message segment (Patient Visit - Additional Information)
   */
  function addPV2(CSejour $sejour = null) {
    $PV2 = CHL7v2Segment::create("PV2", $this->message);
    $PV2->sejour = $sejour;
    $PV2->build($this);
  }
  
  /*
   * Represents an HL7 MRG message segment (Merge Patient Information)
   */
  function addMRG(CPatient $patient_eliminee) {
    $MRG = CHL7v2Segment::create("MRG", $this->message);
    $MRG->patient_eliminee = $patient_eliminee;
    $MRG->build($this);
  }
  
  /*
   * Represents an HL7 ZBE message segment (Movement)
   */
  function addZBE(CSejour $sejour = null) {
    $ZBE = CHL7v2Segment::create("ZBE", $this->message);
    $ZBE->sejour = $sejour;
    $ZBE->uf     = $sejour->getCurrentUF();
    $ZBE->build($this);
  }

  /*
   * Represents an HL7 ZFP message segment (Situation professionnelle)
   */
  function addZFP(CSejour $sejour = null) {
    $ZFP = CHL7v2Segment::create("ZFP", $this->message);
    $ZFP->patient = $sejour->_ref_patient;
    $ZFP->build($this);
  }
  
  /*
   * Represents an HL7 ZFV message segment (Compléments d'information sur la venue)
   */
  function addZFV(CSejour $sejour = null) {
    $ZFV = CHL7v2Segment::create("ZFV", $this->message);
    $ZFV->sejour = $sejour;
    $ZFV->build($this);
  }
  
  /*
   * Represents an HL7 ZFM message segment (Mouvement PMSI)
   */
  function addZFM(CSejour $sejour = null) {
    $ZFM = CHL7v2Segment::create("ZFM", $this->message);
    $ZFM->sejour = $sejour;
    $ZFM->build($this);
  }
  
  /*
   * Represents an HL7 ZFD message segment (Complément démographique)
   */
  function addZFD(CSejour $sejour = null) {
    $ZFD = CHL7v2Segment::create("ZFD", $this->message);
    $ZFD->patient = $sejour->_ref_patient;
    $ZFD->build($this);
  }
}

?>