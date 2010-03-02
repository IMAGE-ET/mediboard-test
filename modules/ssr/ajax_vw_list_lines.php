<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$category_id = CValue::get("category_id");
$full_line_id = CValue::get("full_line_id");

$line = new CPrescriptionLineElement();
$ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
$where["prescription_id"] = " = '$prescription_id'";
$where["element_prescription.category_prescription_id"] = " = '$category_id'";
$lines[$category_id] = $line->loadList($where, null, null, null, $ljoin);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("full_line_id", $full_line_id);
$smarty->assign("lines", $lines);
$smarty->assign("category_id", $category_id);
$smarty->assign("nodebug", true);
$smarty->display("inc_list_lines.tpl");

?>