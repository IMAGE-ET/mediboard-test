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

CCanDo::checkRead();

$patient_id = CValue::get("patient_id");

$patient = new CPatient();
$patient->load($patient_id);

$vcard = new CMbvCardExport();
$vcard->saveVCard($patient);
