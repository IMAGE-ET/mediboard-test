<?php /* $Id: vw_dossier.php 3085 2007-12-18 14:54:45Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: 3085 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
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