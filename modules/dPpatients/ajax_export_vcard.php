<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$patient_id = CValue::get("patient_id");

$patient = new CPatient();
$patient->load($patient_id);

$vcard = new CMbvCardExport();
$vcard->saveVCard($patient);
