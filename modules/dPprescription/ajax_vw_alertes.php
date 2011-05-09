<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$level = CValue::get("level", "medium");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$alert = new CAlert();
$where = array();
$where["handled"] = " = '0'";
$where["level"] = " = '$level'";
$where["prescription.prescription_id"] = " = '$prescription_id'";

$ljoin["prescription_line_medicament"] = "(prescription_line_medicament.prescription_line_medicament_id = alert.object_id) 
                                         AND (alert.object_class = 'CPrescriptionLineMedicament')";
                                         
$ljoin["prescription_line_element"] = "(prescription_line_element.prescription_line_element_id = alert.object_id) 
                                       AND (alert.object_class = 'CPrescriptionLineElement')";
                                         
$ljoin["prescription_line_mix"] = "(prescription_line_mix.prescription_line_mix_id = alert.object_id) 
                                   AND (alert.object_class = 'CPrescriptionLineMix')";                   
                                         
$ljoin["prescription_line_comment"] = "(prescription_line_comment.prescription_line_comment_id = alert.object_id) 
                                       AND (alert.object_class = 'CPrescriptionLineComment')";      

$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                      (prescription_line_element.prescription_id = prescription.prescription_id) OR
                      (prescription_line_mix.prescription_id = prescription.prescription_id) OR
                      (prescription_line_comment.prescription_id = prescription.prescription_id)";

$alertes = $alert->loadList($where, null, null, null, $ljoin);                      

$smarty = new CSmartyDP;
$smarty->assign("alertes", $alertes);
$smarty->assign("level", $level);
$smarty->assign("sejour_id", $prescription->object_id);
$smarty->assign("prescription_id", $prescription_id);
$smarty->display("inc_vw_alertes.tpl");

?>