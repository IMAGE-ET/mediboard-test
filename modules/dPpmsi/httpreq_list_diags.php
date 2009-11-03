<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$sejour_id = CValue::getOrSession("sejour_id");
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefDossierMedical();
$sejour->_ref_dossier_medical->updateFormFields();
$sejour->loadRefPatient();

$patient =& $sejour->_ref_patient;
$patient->loadRefDossierMedical();
$patient->_ref_dossier_medical->updateFormFields();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $sejour->_ref_patient);
$smarty->assign("sejour" , $sejour);

$smarty->display("inc_list_diags.tpl");

?>