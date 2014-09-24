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
 * Tools for state patient
 */
class CPatientStateTools {

  /**
   * Set the PROV status for the patient stateless
   *
   * @param String $state patient state
   *
   * @return int
   */
  static function createStatus($state="PROV") {
    $ds = CSQLDataSource::get("std");

    $ds->exec("UPDATE `patients` SET `status`='$state' WHERE `status` IS NULL;");

    return $ds->affectedRows();
  }

  /**
   * Get the number patient stateless
   *
   * @return int
   */
  static function verifyStatus() {
    $patient = new CPatient();
    $where = array(
      "status" => "IS NULL"
    );

    return $patient->countList($where);
  }
}
