<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6171 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$patient_id   = CValue::get("patient_id");
$sejour_id    = CValue::get("sejour_id");

$patient = new CPatient();
$patient->load($patient_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("sejour", $sejour);

$smarty->display("inc_check_ald.tpl");

?>