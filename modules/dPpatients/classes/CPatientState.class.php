<?php

/**
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * State of the patient
 */
class CPatientState extends CMbObject {
  /** @var integer Primary key */
  public $patient_state_id;

  public $patient_id;
  public $mediuser_id;
  public $state;
  public $datetime;
  public $reason;

  /** @var CPatient */
  public $_ref_patient;
  /** @var CMediusers */
  public $_ref_mediuser;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "patient_state";
    $spec->key    = "patient_state_id";
    return $spec;
  }

  /**
   * @see parent::getProps();
   */
  function getProps() {
    $props = parent::getProps();

    $props["patient_id"]  = "ref class|CPatient notNull cascade";
    $props["mediuser_id"] = "ref class|CMediusers notNull";
    $props["state"]       = "enum list|PROV|VALI|DPOT|ANOM|CACH notNull";
    $props["datetime"]    = "dateTime notNull";
    $props["reason"]      = "text";

    return $props;
  }

  /**
   * Load the patient
   *
   * @return CPatient|null
   */
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
  }

  /**
   * Load the creator of the state
   *
   * @return CMediusers|null
   */
  function loadRefMediuser() {
    return $this->_ref_mediuser = $this->loadFwdRef("mediuser_id");
  }

  /**
   * Store the state of the patient
   *
   * @param CPatient $patient Patient
   *
   * @return null|string
   */
  static function storeState($patient) {
    $identity_status = CAppUI::conf("dPpatients CPatient manage_identity_status", CGroups::loadCurrent());

    //Si la configuration n'est pas activé
    if (!$identity_status) {
      return null;
    }

    $last_state = $patient->loadLastState();

    if ($last_state && $patient->status == $last_state->state) {
      return null;
    }

    $patient_state = new self;
    $patient_state->patient_id = $patient->_id;
    $patient_state->state      = $patient->status;
    $patient_state->reason     = $patient->_reason_state;
    if ($msg = $patient_state->store()) {
      return $msg;
    }

    if ($patient->status == "DPOT") {
      $doubloons = is_array($patient->_doubloon_ids) ? $patient->_doubloon_ids : explode("|", $patient->_doubloon_ids);
      foreach ($doubloons as $_id) {
        $patient_link = new CPatientLink();
        $patient_link->patient_id1 = $patient->_id;
        $patient_link->patient_id2 = $_id;
        $patient_link->loadMatchingObject();
        $patient_link->store();

        $patient_doubloon = new CPatient();
        $patient_doubloon->load($_id);
        $patient_doubloon->status = "DPOT";
        $patient_doubloon->store();
      }
    }

    return null;
  }

  /**
   * Return the State of the patient
   *
   * @param CPatient $patient patient
   *
   * @return null|string
   */
  static function getState(CPatient $patient) {

    $identity_status = CAppUI::conf("dPpatients CPatient manage_identity_status", CGroups::loadCurrent());

    //Si la configuration n'est pas activé
    if (!$identity_status) {
      return null;
    }

    if ($patient->_status_no_guess) {
      return $patient->status;
    }

    if ($patient->vip || $patient->fieldModified("vip")) {
      return $patient->vip == "1" ? "CACH" : "PROV";
    }

    if ($patient->_merging && $patient->countPatientLinks() == 0) {
      return "PROV";
    }

    if ($patient->_anonyme) {
      return "ANOM";
    }

    if (!$patient->_id && !$patient->_doubloon_ids) {
      return "PROV";
    }

    if ($patient->_doubloon_ids) {
      return "DPOT";
    }

    return null;
  }

  /**
   * @see parent::store()
   */
  function store() {

    if (!$this->_id) {
      $this->datetime    = $this->datetime    ?: CMbDT::dateTime();
      $this->mediuser_id = $this->mediuser_id ?: CMediusers::get()->_id;
    }

    if ($msg = parent::store()) {
      return $msg;
    }

    return null;
  }
}
