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

  static $list_state = array("PROV", "VALI", "DPOT", "ANOM", "CACH");

  public $patient_id;
  public $mediuser_id;
  public $state;
  public $datetime;
  public $reason;

  //filter
  public $_date_min;
  public $_date_max;
  public $_number_day;
  public $_date_end;

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
    $props["state"]       = "enum list|".implode("|", self::$list_state)." notNull";
    $props["datetime"]    = "dateTime notNull";
    $props["reason"]      = "text";

    //filter
    $props["_date_min"]    = "dateTime";
    $props["_date_max"]    = "dateTime";
    $props["_date_end"]    = "date";
    $props["_number_day"]  = "num";

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
   * Get the number patient by a state and the filter
   *
   * @param String[] $where    Clause
   * @param String[] $leftjoin Jointure
   *
   * @return Int
   */
  static function getNumberPatient($where, $leftjoin) {
    $ds = CSQLDataSource::get("std");
    $request = new CRequest();
    $request->addSelect("COUNT(DISTINCT(patients.patient_id))");
    $request->addTable("patients");
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    return $ds->loadResult($request->makeSelect());
  }

  /**
   * Get all number patient by a state and the filter
   *
   * @param String $date_min Date minimum
   * @param String $date_max Date maximum
   *
   * @return array
   */
  static function getAllNumberPatient($date_min = null, $date_max = null) {
    $patients_count = array();
    $leftjoin       = null;
    $where          = array();

    if ($date_min) {
      $where["entree"] = ">= '$date_min'";
      $leftjoin["sejour"] = "patients.patient_id = sejour.patient_id";
    }

    if ($date_max) {
      $where["entree"] = "<= '$date_max'";
      $leftjoin["sejour"] = "patients.patient_id = sejour.patient_id";
    }

    $ds = CSQLDataSource::get("std");
    $request = new CRequest();
    $request->addSelect("`status`, COUNT(DISTINCT(`patients`.`patient_id`)) as `total`");
    $request->addTable("patients");
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup("`status`");
    $result = $ds->loadList($request->makeSelect());
    $state_count = array();
    foreach ($result as $_result) {
      $state_count[$_result["status"]] = $_result["total"];
    }

    foreach (self::$list_state as $_state) {
      $patients_count[CMbString::lower($_state)] = CMbArray::get($state_count, $_state, 0);
    }

    return $patients_count;
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
    $patient->completeField("status");
    $identity_status = CAppUI::conf("dPpatients CPatient manage_identity_status", CGroups::loadCurrent());

    //Si la configuration n'est pas activé
    if (!$identity_status) {
      return null;
    }

    if ($patient->_status_no_guess) {
      return $patient->status;
    }

    if (!$patient->_id && $patient->vip) {
      return "CACH";
    }

    if ($patient->_id && $patient->fieldModified("vip")) {
      if ($patient->status != "VALI") {
        return $patient->vip == "1" ? "CACH" : "PROV";
      }
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
