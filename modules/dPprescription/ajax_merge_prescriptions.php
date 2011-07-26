<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_base_id = CValue::get("prescription_base_id");
$prescriptions_ids = CValue::get("prescriptions_ids");

$prescriptions = array();

foreach ($prescriptions_ids as $_prescription_id) {
  $prescription = new CPrescription;
  $prescription->load($_prescription_id);
  $prescription->loadRefsLinesMedComments();
  $prescription->loadRefsLinesElementsComments();
  $prescription->loadRefsPrescriptionLineMixes();
  foreach ($prescription->_ref_prescription_line_mixes as $_line) {
    $_line->loadRefsLines();
    $_line->loadRefPraticien();
  }
  $prescriptions[] = $prescription;
}

$chapitres = array();

// Comptage des lignes par chapitre
foreach($prescriptions as $_prescription) {
  if (!isset($chapitres["med"])) {
    $chapitres["med"] = 0;
  }
  $chapitres["med"] += count($_prescription->_ref_lines_med_comments["med"]) +
                       count($_prescription->_ref_lines_med_comments["comment"])+
                       count($_prescription->_ref_prescription_line_mixes);
  
  if (count($_prescription->_ref_lines_elements_comments)) {
    foreach ($_prescription->_ref_lines_elements_comments as $chap_key=>&$chapitre) {
      if (!isset($chapitres[$chap_key])) {
        $chapitres[$chap_key] = 0;
      }
      foreach ($chapitre as &$cat) {
        foreach ($cat as &$_elements) {
          $chapitres[$chap_key] += count($_elements);
        }
      }
    }
  }
}

$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

$smarty = new CSmartyDP;

$smarty->assign("prescriptions", $prescriptions);
$smarty->assign("prescription", reset($prescriptions));
$smarty->assign("prescriptions_ids", implode("-", $prescriptions_ids));
$smarty->assign("prescription_base_id", $prescription_base_id);
$smarty->assign("chapitres", $chapitres);
$smarty->assign("now", mbDate());
$smarty->assign("sejour", reset($prescriptions)->_ref_object);
$smarty->assign("moments", $moments);
$smarty->assign("checked_lines", 1);
$smarty->display("inc_merge_prescriptions.tpl");

?>