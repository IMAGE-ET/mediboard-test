<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Fabien Ménager
*/

$patient_id = CValue::get('patient_id');
$mode = CValue::get('mode', 'read');

$patient = new CPatient();
$patient->load($patient_id);
$patient->updateFormFields();
$patient->loadRefPhotoIdentite();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('patient', $patient);
$smarty->assign('mode', $mode);
$smarty->display("inc_vw_photo_identite.tpl");

?>
