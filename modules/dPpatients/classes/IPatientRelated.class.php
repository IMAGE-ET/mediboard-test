<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Patient Related interface, can be used on any class linked to a patient
 */
interface IPatientRelated {
  /**
   * Loads the related patient, wether it is a far or a close reference
   *
   * @return CPatient
   */
  function loadRelPatient();
}
