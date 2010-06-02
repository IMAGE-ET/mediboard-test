<?php /* $Id:  $ */


/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$executant_prescription_line_id = CValue::getOrSession("executant_prescription_line_id");
$category_id = CValue::getOrSession("category_id");

$executant_prescription_line = new CExecutantPrescriptionLine();
$executant_prescription_line->load($executant_prescription_line_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("executant_prescription_line", $executant_prescription_line);
$smarty->assign("category_id", $category_id);
$smarty->display("inc_form_executant.tpl");

?>