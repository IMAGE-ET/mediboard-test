<?php /* $Id: httpreq_do_add_insee.php 2342 2007-07-19 14:24:59Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2342 $
* @author SARL OpenXtrem
*/

global $can;
$can->needsAdmin();

$patient_id = CValue::get("patient_id");

$patient = new CPatient();
$patient->load($patient_id);

$vcard = new CMbvCardExport($patient);
$vcard->saveVCard();

?>