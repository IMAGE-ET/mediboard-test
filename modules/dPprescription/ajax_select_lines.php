<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$protocole_id    = CValue::get("protocole_id");
$pratSel_id      = CValue::get("pratSel_id");
$praticien_id    = CValue::get("praticien_id");

$prescription = new CPrescription;
$prescription->load($prescription_id);

$prescription->loadRefsLinesMedComments("1", "", $protocole_id);
$prescription->loadRefsLinesElementsComments("1", "", "", $protocole_id);
$prescription->loadRefsPrescriptionLineMixes("", 1, 1, $protocole_id);
$prescription->countLinesMedsElements(null, null, $protocole_id);

foreach($prescription->_ref_prescription_line_mixes as $_line_mix) {
  $_line_mix->loadRefsLines();
}

$smarty = new CSmartyDP;
$smarty->assign("prescription"   , $prescription);
$smarty->assign("pratSel_id"     , $pratSel_id);
$smarty->assign("praticien_id"   , $praticien_id);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("now", mbDate());
$smarty->display("inc_select_lines.tpl");

?>