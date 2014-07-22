<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPrescriptionSejour();

$prescription = $sejour->_ref_prescription_sejour;

$where = array();
$ljoin = array();
$ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
$ljoin["sejour_task"] = "sejour_task.prescription_line_element_id = prescription_line_element.prescription_line_element_id";
$where["prescription_id"]            = " = '$prescription->_id'";
$where["element_prescription.rdv"]   = " = '1'";
$where["sejour_task.sejour_task_id"] = "IS NULL";
$where["active"]     = " = '1'";
$where["child_id"]   = " IS NULL";
$where["date_arret"] = "IS NULL";
$where["time_arret"] = "IS NULL";


$line_element = new CPrescriptionLineElement();
$lines = $line_element->loadList($where, null, null, null, $ljoin); 

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->display("inc_vw_lines_without_task.tpl");

