<?php

/***
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

  /** @var string */
  public $event_type = "ADT";

  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADT
   */
  function __construct($i18n = null) {
    parent::__construct($i18n);
    
    $this->profil      = $i18n ? "PAM_$i18n" : "PAM";
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_{$this->struct_code}"
      )
    );
    $this->transaction = CIHE::getPAMTransaction($this->code, $i18n);
  }
  
  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);
        
    // Message Header 
    $this->addMSH();

    // Event Type
    $this->addEVN($this->getEVNPlannedDateTime($object), $this->getEVNOccuredDateTime($object));
  }
  
  /**
   * Get event planned datetime
   *
   * @param CMbObject $object Object to use
   *
   * @return DateTime Event planned
   */
  function getEVNPlannedDateTime($object) {
  }
  
  /**
   * Get event planned datetime
   *
   * @param CMbObject $object Object to use
   *
   * @return DateTime Event occured
   */
  function getEVNOccuredDateTime($object) {
  }
  
  /**
   * MSH - Represents an HL7 MSH message segment (Message Header)
   *
   * @return void
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }
  
  /**
   * Represents an HL7 EVN message segment (Event Type)
   *
   * @param string $planned_datetime event planned datetime
   * @param string $occured_datetime event occured datetime
   *
   * @return void
   */
  function addEVN($planned_datetime = null, $occured_datetime = null) {
    /** @var CHL7v2SegmentEVN $EVN */
    $EVN = CHL7v2Segment::create("EVN", $this->message);
    $EVN->planned_datetime = $planned_datetime;
    $EVN->occured_datetime = $occured_datetime;
    $EVN->build($this);
  }
  
  /**
   * Represents an HL7 PID message segment (Patient Identification)
   *
   * @param CPatient $patient Patient
   * @param CSejour  $sejour  Admit
   *
   * @return void
   */
  function addPID(CPatient $patient, CSejour $sejour = null) {
    $segment_name = $this->_is_i18n ? "PID_FR" : "PID";

    /** @var CHL7v2SegmentPID $PID */
    $PID = CHL7v2Segment::create($segment_name, $this->message);
    $PID->patient = $patient;
    $PID->sejour = $sejour;
    $PID->set_id  = 1;
    $PID->build($this);
  }
  
  /**
   * Represents an HL7 PD1 message segment (Patient Additional Demographic)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addPD1(CPatient $patient) {
    /** @var CHL7v2SegmentPD1 $PD1 */
    $PD1 = CHL7v2Segment::create("PD1", $this->message);
    $PD1->patient = $patient;
    $PD1->build($this);
  }
  
  /**
   * Represents an HL7 ROL message segment (Role)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addROLs(CPatient $patient) {
    $patient->loadRefsCorrespondants();
    if ($patient->_ref_medecin_traitant->_id) {
      /** @var CHL7v2SegmentROL $ROL */
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

      /** @var CHL7v2SegmentROL $ROL */
      $ROL = CHL7v2Segment::create("ROL", $this->message);
      $ROL->medecin = $medecin;
      $ROL->role_id = "RT";
      $ROL->build($this);
    }
  }
  
  /**
   * Represents an HL7 NK1 message segment (Next of Kin / Associated Parties)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addNK1s(CPatient $patient) {
    $i = 1;
    foreach ($patient->loadRefsCorrespondantsPatient() as $_correspondant) {
      /** @var CHL7v2SegmentNK1 $NK1 */

      $NK1 = CHL7v2Segment::create("NK1", $this->message);
      $NK1->set_id = $i;
      $NK1->correspondant = $_correspondant;
      $NK1->build($this);
      $i++;
    }
  }
  
  /**
   * Represents an HL7 PV1 message segment (Patient Visit)
   *
   * @param CSejour $sejour Admit
   * @param int     $set_id Set ID
   *
   * @return void
   */
  function addPV1(CSejour $sejour = null, $set_id = 1) {
    $segment_name = $this->_is_i18n ? "PV1_FR" : "PV1";

    /** @var CHL7v2SegmentPV1 $PV1 */
    $PV1          = CHL7v2Segment::create($segment_name, $this->message);
    $PV1->sejour  = $sejour;
    $PV1->set_id  = $set_id;

    if ($sejour) {
      $PV1->curr_affectation = $sejour->_ref_hl7_affectation;
    }
    $PV1->build($this);
  }
  
  /**
   * Represents an HL7 PV2 message segment (Patient Visit - Additional Information)
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function addPV2(CSejour $sejour = null) {
    /** @var CHL7v2SegmentPV2 $PV2 */
    $PV2            = CHL7v2Segment::create("PV2", $this->message);
    $PV2->sejour    = $sejour;

    if ($sejour) {
      $PV2->curr_affectation = $sejour->_ref_hl7_affectation;
    }
    $PV2->build($this);
  }
  
  /**
   * Represents an HL7 MRG message segment (Merge Patient Information)
   *
   * @param CPatient $patient_eliminee Patient to destroy
   *
   * @return void
   */
  function addMRG(CPatient $patient_eliminee) {
    /** @var CHL7v2SegmentMRG $MRG */
    $MRG = CHL7v2Segment::create("MRG", $this->message);
    $MRG->patient_eliminee = $patient_eliminee;
    $MRG->build($this);
  }
  
  /**
   * Represents an HL7 ZBE message segment (Movement)
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function addZBE(CSejour $sejour = null) {
    $segment_name = $this->_is_i18n ? "ZBE_FR" : "ZBE";

    /** @var CHL7v2SegmentZBE $ZBE */
    $ZBE          = CHL7v2Segment::create($segment_name, $this->message);
    $ZBE->sejour  = $sejour;
    $movement     = $sejour->_ref_hl7_movement;
    $affectation  = new CAffectation();
    if ($movement && $movement->affectation_id) {
      $affectation->load($movement->affectation_id);
    }
    $ZBE->curr_affectation  = $affectation;
    $ZBE->movement          = $movement;
    $ZBE->other_affectation = $sejour->_ref_hl7_affectation;
    $ZBE->build($this);
  }

  /**
   * Represents an HL7 ZFP message segment (Situation professionnelle)
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function addZFP(CSejour $sejour = null) {
    /** @var CHL7v2SegmentZFP $ZFP */
    $ZFP = CHL7v2Segment::create("ZFP", $this->message);
    $ZFP->patient = $sejour->_ref_patient;
    $ZFP->build($this);
  }
  
  /**
   * Represents an HL7 ZFV message segment (Compléments d'information sur la venue)
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function addZFV(CSejour $sejour = null) {
    /** @var CHL7v2SegmentZFV $ZFV */
    $ZFV = CHL7v2Segment::create("ZFV", $this->message);
    $ZFV->sejour = $sejour;
    $ZFV->build($this);
  }
  
  /**
   * Represents an HL7 ZFM message segment (Mouvement PMSI)
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function addZFM(CSejour $sejour = null) {
    /** @var CHL7v2SegmentZFM $ZFM */
    $ZFM = CHL7v2Segment::create("ZFM", $this->message);
    $ZFM->sejour = $sejour;
    $ZFM->build($this);
  }
  
  /**
   * Represents an HL7 ZFD message segment (Complément démographique)
   *
   * @param CSejour $sejour Admit
   *
   * @return void
   */
  function addZFD(CSejour $sejour = null) {
    /** @var CHL7v2SegmentZFD $ZFD */
    $ZFD = CHL7v2Segment::create("ZFD", $this->message);
    $ZFD->patient = $sejour->_ref_patient;
    $ZFD->build($this);
  }
  
  /**
   * Represents an HL7 GT1 message segment (Guarantor)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addGT1(CPatient $patient = null) {
    
  }
}