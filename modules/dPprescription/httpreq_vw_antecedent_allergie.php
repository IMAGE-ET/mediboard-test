<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;
$dossier_medical->loadRefsAntecedents();
$dossier_medical->countAntecedents();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("antecedents", $dossier_medical->_ref_antecedents);
$smarty->assign("sejour_id", $sejour->_id);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->display("inc_vw_antecedent_allergie.tpl");

?>

