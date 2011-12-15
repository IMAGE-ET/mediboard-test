<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id  = CValue::get("prescription_id");

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement des medicaments
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();
$prescription->loadRefsPrescriptionLineMixes();

$prescription->countLinesMedsElements();

// Suppression des lignes non arretes
foreach ($prescription->_ref_lines_med_comments["med"] as $key => $_line) {
  if($_line->date_arret || !$_line->signee) {
    unset($prescription->_ref_lines_med_comments["med"][$key]);
    $prescription->_counts_by_chapitre["med"] --;
  } 
}

$prescription->_ref_lines_med_comments["comment"] = array();

if ($prescription->_ref_lines_elements_comments) {
  foreach ($prescription->_ref_lines_elements_comments as $chap_key=>&$chapitre) {
    foreach ($chapitre as &$cat) {
      foreach ($cat as &$_elements) {
        foreach ($_elements as $element_key=>$_line) {
          if($_line->date_arret || !$_line->signee) {
            unset($_elements[$element_key]);
            $prescription->_counts_by_chapitre[$chap_key] --;
          }
        }
      }
    }
  }
}

foreach ($prescription->_ref_prescription_line_mixes as $key=>$_line_mix) {
  if($_line_mix->date_arret || !$_line_mix->signature_prat) {
    $prescription->_counts_by_chapitre["med"] -= $_line_mix->countBackRefs("lines_mix");
    unset($prescription->_ref_prescription_line_mixes[$key]);
  }
  else {
    $_line_mix->loadRefsLines();
    $_line_mix->loadRefPraticien();
  }
}

$prescription->loadRefObject();

$smarty = new CSmartyDP;
$smarty->assign("prescription"   , $prescription);
$smarty->assign("praticien_id"   , CAppUI::$user->_id);
$smarty->assign("pratSel_id"     , CAppUI::$user->_id);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("sejour"         , $prescription->_ref_object);
$smarty->assign("now"            , mbDate());
$smarty->assign("mode"           , "stop_lines");
$smarty->display("inc_select_lines.tpl");
?>