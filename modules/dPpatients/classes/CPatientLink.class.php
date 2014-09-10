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
 * Link between patient
 */
class CPatientLink extends CMbObject {
  /** @var integer Primary key */
  public $patient_link_id;

  public $patient_id1;
  public $patient_id2;
  public $type;
  public $reason;

  /** @var CMediusers */
  public $_ref_mediuser;

  /** @var CPatient */
  public $_ref_patient1;
  /** @var CPatient */
  public $_ref_patient2;
  /** @var  Cpatient */
  public $_ref_patient_doubloon;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "patient_link";
    $spec->key    = "patient_link_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["patient_id1"] = "ref class|CPatient notNull cascade";
    $props["patient_id2"] = "ref class|CPatient notNull cascade";
    $props["type"]        = "enum list|DPOT default|DPOT";
    $props["reason"]      = "text";

    return $props;
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
   * Load the first patient
   *
   * @return CPatient|null
   */
  function loadRefPatient1() {
    return $this->_ref_patient1 = $this->loadFwdRef("patient_id1");
  }

  /**
   * Load the second patient
   *
   * @return CPatient|null
   */
  function loadRefPatient2() {
    return $this->_ref_patient2 = $this->loadFwdRef("patient_id2");
  }

  /**
   * @see parent::check()
   */
  function check() {

    if (!$this->_forwardRefMerging) {
      $patient_link = new self;
      $patient_link->patient_id1 = $this->patient_id2;
      $patient_link->patient_id2 = $this->patient_id1;
      if ($patient_link->countMatchingList() > 0) {
        //todo trad
        return "Lien déjà existant";
      }
    }

    return parent::check();
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    $this->completeField("patient_id1", "patient_id2");

    $patient1 = $this->loadRefPatient1();
    $patient2 = $this->loadRefPatient2();
    if ($msg = parent::delete()) {
      return $msg;
    }

    if (!$patient1->loadPatientLinks()) {
      $patient1->status = $patient1->vip ? "VIP": "PROV";
      $patient1->store();
    }

    if (!$patient2->loadPatientLinks()) {
      $patient2->status = $patient2->vip ? "VIP": "PROV";
      $patient2->store();
    }

    return null;
  }


  /**
   * Delete the doubloons of patient link
   *
   * @return void
   */
  static function deleteDoubloon() {
    $ds = CSQLDataSource::get("std");

    //Suppression des liens pointant sur le même patient 3->3
    $query = "DELETE FROM `patient_link`
                WHERE patient_id1 = patient_id2";
    $ds->exec($query);

    //Suppression des liens en double 2->3 2->3
    $query = "DELETE t1 FROM `patient_link` as t1, `patient_link` as t2
                WHERE t1.patient_id1 = t2.patient_id1
                AND t1.patient_id2 = t2.patient_id2
                AND t1.patient_link_id > t2.patient_link_id";
    $ds->exec($query);

    //Suppression des liens ayant un lien réciproque 2->3 3->2
    $query = "DELETE t1 FROM `patient_link` as t1, `patient_link` as t2
                WHERE t1.patient_id1 = t2.patient_id2
                AND t1.patient_id2 = t2.patient_id1
                AND t1.patient_link_id > t2.patient_link_id";
    $ds->exec($query);
  }
}