<?php

/**
 * Patient Administration Management IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CPAM 
 * Patient Administration Management
 */
class CPAM extends CIHE {
  /**
   * @var array
   */
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5"  
  );

  /**
   * @var array
   */
  static $transaction_iti30 = array(
    "A08", "A24", "A37", "A28", "A29", "A31", "A40", "A46", "A47"
  );

  /**
   * @var array
   */
  static $transaction_iti31 = array(
    "A01", "A02", "A03", "A04", "A05", "A06", "A07", "A08", "A11", "A12", "A13", "A14", "A16", "A21", "A22", "A25", "A38", "A44",
    "A52", "A53", "A54", "A55", "Z99"
  );

  /**
   * @var array
   */
  static $evenements = array(
    // ITI-30
    "A24" => "CHL7EventADTA24",
    "A28" => "CHL7EventADTA28",
    "A29" => "CHL7EventADTA29",
    "A31" => "CHL7EventADTA31",
    "A37" => "CHL7EventADTA37",
    "A40" => "CHL7EventADTA40",
    "A46" => "CHL7EventADTA46",
    "A47" => "CHL7EventADTA47",
    
    // ITI-31
    "A01" => "CHL7EventADTA01",
    "A02" => "CHL7EventADTA02",
    "A03" => "CHL7EventADTA03",
    "A04" => "CHL7EventADTA04",
    "A05" => "CHL7EventADTA05",
    "A06" => "CHL7EventADTA06",
    "A07" => "CHL7EventADTA07",
    "A08" => "CHL7EventADTA08",
    "A11" => "CHL7EventADTA11",
    "A12" => "CHL7EventADTA12",
    "A13" => "CHL7EventADTA13",
    "A14" => "CHL7EventADTA14",
    "A16" => "CHL7EventADTA16",
    "A21" => "CHL7EventADTA21",
    "A22" => "CHL7EventADTA22",
    "A25" => "CHL7EventADTA25",
    "A38" => "CHL7EventADTA38",
    "A44" => "CHL7EventADTA44",
    "A52" => "CHL7EventADTA52",
    "A53" => "CHL7EventADTA53",
    "A54" => "CHL7EventADTA54",
    "A55" => "CHL7EventADTA55",
    "Z99" => "CHL7EventADTZ99",
  );

  /**
   * Construct
   *
   * @return \CPAM
   */
  function __construct() {
    $this->type = "PAM";

    $this->_categories = array(
      "ITI-30" => self::$transaction_iti30,
      "ITI-31" => self::$transaction_iti31,
    );
  }
  
  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }
  
  /**
   * Retrieve transaction name
   *
   * @param string $code Event code
   *
   * @return string|null Transaction name
   */
  static function getTransaction($code) {
    if (in_array($code, self::$transaction_iti30)) {
      return "ITI30";
    }
    
    if (in_array($code, self::$transaction_iti31)) {
      return "ITI31";
    }

    return null;
  }
  
  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object|null An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code    = $exchange->code;
    $version = $exchange->version;
    
    foreach (CHL7::$versions as $_version => $_sub_versions) {      
      if (in_array($version, $_sub_versions)) {
        $classname = "CHL7{$_version}EventADT$code";
        return new $classname;
      }
    }

    return null;
  }

  /**
   * Test A24 - Link the two patients
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA24(CCnStep $step) {
    //  PDS-PAM_Identification_Mgt_Link : Récupération du step 10
    $patient_1 = self::loadPatientPDS($step, 10);

    //  PDS-PAM_Identification_Mgt_Link : Récupération du step 10
    $patient_2 = self::loadPatientPDS($step, 40);

    $patient_1->patient_link_id = $patient_2->_id;

    if ($msg = $patient_1->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A28 - Create patient with full demographic data
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA28(CCnStep $step) {
    // PDS-PAM_Identification_Mgt_Merge
    $patient = new CPatient();
    // Random sur les champs du patient
    $patient->random();

    $test    = $step->_ref_test;
    $partner = $test->_ref_partner;

    // On sélectionne le nom du patient en fonction du partenaire, du test et de l'étape
    $patient->nom = "{$partner->name}_{$test->_id}_{$step->number}";

    if ($msg = $patient->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A31 - Update patient demographics
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA31(CCnStep $step) {
    // PDS-PAM_Identification_Mgt_Merge : Récupération du step 10
    $patient = self::loadPatientPDS($step, 10);

    $patient->prenom = "CHANGE_$patient->prenom";
    if ($msg = $patient->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A37 - Unlink the two previously linked patients
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA37(CCnStep $step) {
    //  PDS-PAM_Identification_Mgt_Link : Récupération du step 10
    $patient = self::loadPatientPDS($step, 10);

    $patient->patient_link_id = "";

    if ($msg = $patient->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A40 - Merge the two patients
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA40(CCnStep $step) {
    if ($step == "ITI-30") {
      // PDS-PAM_Identification_Mgt_Merge : Récupération du step 10
      $patient_1        = self::loadPatientPDS($step, 10);
      $first_patient_id = $patient_1->_id;

      // PDS-PAM_Identification_Mgt_Merge : Récupération du step 40
      $patient_2 = self::loadPatientPDS($step, 40);
    }
    else {
      // PES-PAM_Encounter_Management_Basic
      $patient_1        = self::loadPatientPES($step, 10);
      $first_patient_id = $patient_1->_id;

      // PDS-PAM_Identification_Mgt_Merge : Récupération du step 50
      $patient_2 = self::loadPatientPES($step, 50);
    }

    $patient_2_array = array($patient_2);

    $checkMerge = $patient_1->checkMerge($patient_2_array);
    // Erreur sur le check du merge
    if ($checkMerge) {
      throw new CMbException("La fusion de ces deux patients n'est pas possible à cause des problèmes suivants : $checkMerge");
    }

    $patient_1->_id = $first_patient_id;

    $patient_1->_merging = CMbArray::pluck($patient_2_array, "_id");
    if ($msg = $patient_1->merge($patient_2_array)) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A47 - Changes one of the identifiers
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA47(CCnStep $step) {
    // PDS-PAM_Identification_Mgt_Merge : Récupération du step 10
    $patient = self::loadPatientPDS($step, 10);

    $patient->loadIPP($step->_ref_test->group_id);
    $idex = $patient->_ref_IPP;

    $idex->id400 = rand(1000000, 9999999);
    if ($msg = $idex->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A01 - Admit inpatient
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA01(CCnStep $step) {
    $patient = self::loadPatientPES($step, $step->number);

    $scenario = $step->_ref_test->_ref_scenario;

    $sejour              = new CSejour();
    $sejour->patient_id  = $patient->_id;
    $sejour->group_id    = $step->_ref_test->group_id;

    $timestamp = time() + (rand(1, 30) * rand(1, 24) * rand(1, 60) * rand(1, 60));

    switch ($scenario->option) {
      case 'HISTORIC_MVT' :
        $sejour->entree_prevue = CMbDT::date(strftime(CMbDT::ISO_DATETIME, $timestamp))." 08:00:00";
        break;

      default :
        $sejour->entree_prevue = strftime(CMbDT::ISO_DATETIME, $timestamp);
        break;
    }

    $sejour->entree_reelle = $sejour->entree_prevue;
    $sejour->sortie_prevue = CMbDT::dateTime("+4 day", $sejour->entree_reelle);
    $sejour->praticien_id  = $sejour->getRandomValue("praticien_id", true);
    $sejour->type          = "comp";
    $sejour->service_id    = $sejour->getRandomValue("service_id", true);
    $sejour->libelle       = "Séjour ITI-31 - $patient->nom";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A02 - Transfer the patient to a new room
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA02(CCnStep $step) {
    // PES-PAM_Encounter_Management_IN_OUT
    $patient = self::loadPatientPES($step, 40);
    $sejour  = self::loadAdmitPES($patient);

    $affectation             = new CAffectation();
    $affectation->lit_id     = $affectation->getRandomValue("lit_id", true);
    $affectation->sejour_id  = $sejour->_id;
    $affectation->entree     = $sejour->entree;
    $affectation->sortie     = CMbDT::dateTime("+2 day", $affectation->entree);

    if ($msg = $affectation->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A03 - Discharge patient
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA03(CCnStep $step) {
    $scenario = $step->_ref_test->_ref_scenario;

    $step_number = null;
    switch ($scenario->option) {
      case 'HISTORIC_MVT' :
        $step_number = 10;
        break;

      default :
        if ($step->number == 90) {
          $step_number = 30;
        }
        if ($step->number == 100) {
          $step_number = 40;
        }

        break;
    }

    if (!$step_number) {
      throw new CMbException("Aucune étape trouvée");
    }

    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, $step_number);
    $sejour  = self::loadAdmitPES($patient);

    $sejour->sortie_reelle = $sejour->sortie_prevue;

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A04 - Admit outpatient
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA04(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, $step->number);

    $sejour                = new CSejour();
    $sejour->patient_id    = $patient->_id;
    $sejour->group_id      = $step->_ref_test->group_id;

    $timestamp = time() + (rand(1, 30) * rand(1, 24) * rand(1, 60) * rand(1, 60));

    $sejour->entree_prevue = strftime(CMbDT::ISO_DATETIME, $timestamp);
    $sejour->entree_reelle = $sejour->entree_prevue;
    $sejour->sortie_prevue = CMbDT::dateTime("+6 hours", $sejour->entree_reelle);
    $sejour->praticien_id  = $sejour->getRandomValue("praticien_id", true);
    $sejour->type          = "urg";
    $sejour->service_id    = $sejour->getRandomValue("service_id", true);
    $sejour->libelle       = "Séjour ITI-31 - $patient->nom";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A05 - Pre-admit the inpatient
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA05(CCnStep $step) {
    $patient = self::loadPatientPES($step, $step->number);

    $scenario = $step->_ref_test->_ref_scenario;

    $sejour              = new CSejour();
    $sejour->patient_id  = $patient->_id;
    $sejour->group_id    = $step->_ref_test->group_id;

    $timestamp = time() + (rand(1, 30) * rand(1, 24) * rand(1, 60) * rand(1, 60));

    $sejour->entree_prevue = strftime(CMbDT::ISO_DATETIME, $timestamp);
    $sejour->sortie_prevue = CMbDT::dateTime("+4 day", $sejour->entree_prevue);
    $sejour->praticien_id  = $sejour->getRandomValue("praticien_id", true);
    $sejour->type          = "comp";
    $sejour->service_id    = $sejour->getRandomValue("service_id", true);
    $sejour->libelle       = "Séjour ITI-31 - $patient->nom";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A06 - Change patient's class from outpatient (PV1-2 = O) to inpatient (PV1-2 = I)
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA06(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, 40);
    $sejour  = self::loadAdmitPES($patient);

    $sejour->type = "comp";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A07 - Change patient's class from inpatient (PV1-2 = I) to outpatient (PV1-2 = O)
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA07(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, 40);
    $sejour  = self::loadAdmitPES($patient);

    $sejour->type = "exte";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A08 - Update last name
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA08(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, 50);
    $sejour  = self::loadAdmitPES($patient);

    $patient->nom = "PAMUPDATE";

    if ($msg = $patient->store()) {
      throw new CMbException($msg);
    }

    $sejour->libelle = "Séjour ITI-31 - $patient->nom";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A11 - Cancel visit
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA11(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, 20);
    $sejour = self::loadAdmitPES($patient);

    $sejour->entree_reelle = "";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A12 - Cancel the previous transfer
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA12(CCnStep $step) {
    // PES-PAM_Encounter_Management_IN_OUT
    $patient     = self::loadPatientPES($step, 40);
    $sejour      = self::loadAdmitPES($patient);
    $affectation = $sejour->loadRefFirstAffectation();

    if ($msg = $affectation->delete()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A13 - Cancel discharge
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA13(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, 30);
    $sejour = self::loadAdmitPES($patient);

    $sejour->sortie_reelle = "";

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A21 - Gone on a leave of absence
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA21(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $step_number = null;
    $add_day     = 0;
    if ($step->number == 60) {
      $add_day     = 1;
      $step_number = 30;
    }
    if ($step->number == 70) {
      $add_day     = 2;
      $step_number = 20;
    }

    if (!$step_number) {
      throw new CMbException("Aucune étape trouvée");
    }

    $patient = self::loadPatientPES($step, $step_number);
    $sejour  = self::loadAdmitPES($patient);

    $service_externe           = new CService();
    $service_externe->group_id = $step->_ref_test->group_id;
    $service_externe->externe  = 1;
    $service_externe->loadMatchingObject();

    if (!$service_externe->_id) {
      throw new CMbException("Aucun service externe de configuré");
    }

    $affectation             = new CAffectation();
    $affectation->service_id = $service_externe->_id;
    $affectation->sejour_id  = $sejour->_id;
    $affectation->entree     = $sejour->entree;
    $affectation->sortie     = CMbDT::dateTime("+$add_day day", $affectation->entree);

    if ($msg = $affectation->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A22 - Returned from its leave of absence
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA22(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $patient     = self::loadPatientPES($step, 30);
    $sejour      = self::loadAdmitPES($patient);
    $affectation = self::loadLeaveOfAbsence($step, $sejour);

    $affectation->effectue   = 1;

    if ($msg = $affectation->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A38 - Cancel the pre-admission
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA38(CCnStep $step) {
    // PES-PAM_Encounter_Management_Basic
    $patient = self::loadPatientPES($step, 20);
    $sejour  = self::loadAdmitPES($patient);

    $sejour->annule = 1;

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A44 - Moves the account of patient#1 to patient#2
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA44(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $patient_1   = self::loadPatientPES($step, 20);
    $patient_2   = self::loadPatientPES($step, 30);
    $sejour      = self::loadAdmitPES($patient_2);

    $sejour->patient_id = $patient_1->_id;

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A52 - Cancel the leave of absence
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA52(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $patient     = self::loadPatientPES($step, 20);
    $sejour      = self::loadAdmitPES($patient);
    $affectation = self::loadLeaveOfAbsence($step, $sejour);

    if ($msg = $affectation->delete()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A53 - Cancel the return from leave of absence
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA53(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $patient     = self::loadPatientPES($step, 30);
    $sejour      = self::loadAdmitPES($patient);
    $affectation = self::loadLeaveOfAbsence($step, $sejour);

    $affectation->effectue   = 0;

    if ($msg = $affectation->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A54 - Change the name of the attending doctor
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA54(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $patient = self::loadPatientPES($step, 20);
    $sejour  = self::loadAdmitPES($patient);

    do {
      $random_value = $sejour->getRandomValue("praticien_id", true);
    } while ($sejour->praticien_id == $random_value);

    $sejour->praticien_id = $random_value;

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test A55 - Change back the name of the attending doctor to the original one
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testA55(CCnStep $step) {
    // PES-PAM_Encounter_Management_ADVANCE
    $patient = self::loadPatientPES($step, 20);
    $sejour  = self::loadAdmitPES($patient);

    $sejour->praticien_id = $sejour->getValueAtDate($sejour->loadFirstLog()->date, "praticien_id");

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Test Z99 - Update admit
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testZ99(CCnStep $step) {
    $patient = self::loadPatientPES($step, 10);
    $sejour  = self::loadAdmitPES($patient);

    $scenario = $step->_ref_test->_ref_scenario;

    switch ($scenario->option) {
      case 'HISTORIC_MVT' :
        if ($step->number == 30) {
          $sejour->sortie_reelle = CMbDT::date($sejour->sortie)." 11:00:00";
        }
        if ($step->number == 40) {
          $sejour->entree_reelle = CMbDT::date($sejour->entree_reelle)." 07:30:00";
        }
        break;

      default :

        break;
    }

    if ($msg = $sejour->store()) {
      throw new CMbException($msg);
    }
  }

  /**
   * Load patient PDS
   *
   * @param CCnStep $step        Step
   * @param int     $step_number Step number
   *
   * @throws CMbException
   *
   * @return CPatient $patient
   */
  static function loadPatientPDS(CCnStep $step, $step_number) {
    // PDS-PAM_Identification_Mgt_Merge : Récupération du step 10
    $test    = $step->_ref_test;
    $partner = $test->_ref_partner;

    $patient = new CPatient();
    $where = array();
    $where["nom"] = " = '{$partner->name}_{$test->_id}_$step_number'";
    $patient->loadObject($where);

    if (!$patient->_id) {
      throw new CMbException("CPAM-cn_test-no_patient_id");
    }

    return $patient;
  }

  /**
   * Load patient PES
   *
   * @param CCnStep $step        Step
   * @param int     $step_number Step number
   *
   * @throws CMbException
   *
   * @return CPatient $patient
   */
  static function loadPatientPES(CCnStep $step, $step_number) {
    // PES-PAM_Encounter_Management_Basic
    $test    = $step->_ref_test;
    $partner = $test->_ref_partner;

    $name = null;
    switch ($step_number) {
      case 10 :
        $name = "ONE";
        break;
      case 20 :
        $name = "TWO";
        break;
      case 30 :
        $name = "THREE";
        break;
      case 40 :
        $name = "FOUR";
        break;
      case 50 :
        $name = "FIVE";
        break;
    }
    $name = "PAM$name";

    $patient = new CPatient();
    $where = array();
    $where["nom"] = " = '{$name}_{$partner->name}_{$test->_id}'";
    $patient->loadObject($where);

    if (!$patient->_id) {
      $patient->random();
      $patient->nom = "{$name}_{$partner->name}_{$test->_id}";

      if ($msg = $patient->store()) {
        throw new CMbException($msg);
      }
    }

    return $patient;
  }

  /**
   * Load admit PES
   *
   * @param CPatient $patient Person
   *
   * @throws CMbException
   *
   * @return CSejour $sejour
   */
  static function loadAdmitPES(CPatient $patient) {
    $sejour             = new CSejour();

    $where["patient_id"] = " = '$patient->_id'";
    $where["libelle"]    = " = 'Séjour ITI-31 - $patient->nom'";

    $order = "sejour_id DESC";

    $sejour->loadObject($where, $order);

    if (!$sejour->_id) {
      throw new CMbException("La séjour du patient '$patient->nom' n'a pas été retrouvé");
    }

    return $sejour;
  }

  /**
   * Load leave of absence
   *
   * @param CCnStep $step   Step
   * @param CSejour $sejour Admit
   *
   * @throws CMbException
   *
   * @return CAffectation $affectation
   */
  static function loadLeaveOfAbsence(CCnStep $step, CSejour $sejour) {
    $service_externe = CService::loadServiceExterne($step->_ref_test->group_id);

    if (!$service_externe->_id) {
      throw new CMbException("Aucun service externe de configuré");
    }

    $affectation             = new CAffectation();
    $affectation->service_id = $service_externe->_id;
    $affectation->sejour_id  = $sejour->_id;
    $affectation->entree     = $sejour->entree;
    $affectation->loadMatchingObject();

    if (!$affectation->_id) {
      throw new CMbException("Aucune affectation retrouvée");
    }

    return $affectation;
  }
}