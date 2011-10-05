<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$correspondant_id = CValue::get("correspondant_id");
$patient_id       = CValue::get("patient_id");

$correspondant = new CCorrespondantPatient;
$correspondant->patient_id = $patient_id;

if ($correspondant_id) {
  $correspondant->load($correspondant_id);
}

$smarty = new CSmartyDP;
$smarty->assign("correspondant", $correspondant);
$smarty->display("inc_form_correspondant.tpl");

?>