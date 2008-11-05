<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;  
$can->needsEdit();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$widget_id = mbGetValueFromGet("widget_id");

$patient = new CPatient();
$patient->load($patient_id);
if ($patient->_id) {
	$patient->loadRefs();
	$patient->loadRefsCorrespondants();
	foreach ($patient->_ref_medecins_correspondants as &$corr) {
		$corr->loadRefs();
		$corr->_ref_medecin->updateFormFields();
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("widget_id", $widget_id);

$smarty->display("inc_widget_correspondants.tpl");

?>