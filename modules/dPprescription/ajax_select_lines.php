<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$pack_protocole_id    = CValue::get("protocole_id");
$pratSel_id      = CValue::get("pratSel_id");
$praticien_id    = CValue::get("praticien_id");
$ids = CValue::get("ids");

if (!is_array($ids)) {
  $ids = array();
}
$ids[] = $prescription_id;

$pack_protocole = explode("-", $pack_protocole_id);
$pack_id = ($pack_protocole[0] === "pack") ? $pack_protocole[1] : "";
$protocole_id = ($pack_protocole[0] === "prot") ? $pack_protocole[1] : "";

$lines_med_comments = array();
$lines_elt_comments = array();
$lines_mixes = array();
$count_lines_meds = array();
$checked_lines_tab = array();
$prescription = new CPrescription;

if ($protocole_id) {
  $prescription->load($prescription_id);
  $prescription->loadRefsLinesMedComments("1", "", $protocole_id);
  $prescription->loadRefsLinesElementsComments("1", "", "", $protocole_id);
  $prescription->loadRefsPrescriptionLineMixes("", 1, 1, $protocole_id);
  $prescription->countLinesMedsElements(null, null, $protocole_id);
  
  // On instancie le protocole utilisé pour récupérer
  // le champ checked_lines (lignes cochées dans la modale)
  $protocole = new CPrescription;
  $protocole->load($protocole_id);
  $checked_lines_tab[$protocole_id] = $protocole->checked_lines;
}
else if ($pack_id) {
  $pack = new CPrescriptionProtocolePack();
  $pack->load($pack_id);
  $pack->loadRefsPackItems();
  
  foreach($pack->_ref_protocole_pack_items as $_pack_item){
    $protocole_id = $_pack_item->prescription_id;
    $protocole = new CPrescription;
    $protocole->load($protocole_id);
    
    foreach ($ids as $_id) {
      $prescription = new CPrescription;
      $prescription->load($_id);
      
      if ($protocole->type !== $prescription->type) {
        continue;
      }
      
      $prescription->loadRefsLinesMedComments("1", "", $protocole_id);
      if (is_array($prescription->_ref_lines_med_comments)) {
        $lines_med_comments = array_merge_recursive($lines_med_comments, $prescription->_ref_lines_med_comments);
      }
      
      $prescription->loadRefsLinesElementsComments("1", "", "", $protocole_id);
      if (is_array($prescription->_ref_lines_elements_comments)) {
        $lines_elt_comments = array_merge_recursive($lines_elt_comments, $prescription->_ref_lines_elements_comments);
      }
      
      $prescription->loadRefsPrescriptionLineMixes("", 1, 1, $protocole_id);
      if (is_array($prescription->_ref_prescription_line_mixes)) {
        $lines_mixes = array_merge($lines_mixes, $prescription->_ref_prescription_line_mixes);
      }
      
      $prescription->countLinesMedsElements(null, null, $protocole_id);
      
      if (is_array($prescription->_counts_by_chapitre)) {
        foreach($prescription->_counts_by_chapitre as $chap=>$count) {
          @$count_lines_meds[$chap] += $count;
        }
      }
      $checked_lines_tab[$protocole_id] = $protocole->checked_lines;
    }
  }
  
  $prescription->_ref_lines_med_comments = $lines_med_comments;
  $prescription->_ref_lines_elements_comments = $lines_elt_comments;
  $prescription->_ref_prescription_line_mixes = $lines_mixes;
  $prescription->_counts_by_chapitre = $count_lines_meds;
}

// Suppression des lignes signées
foreach ($prescription->_ref_lines_med_comments["med"] as $key=>$_line) {
  if ($_line->signee) {
    unset($prescription->_ref_lines_med_comments["med"][$key]);
    $prescription->_counts_by_chapitre["med"] --;
  }
}

foreach ($prescription->_ref_lines_med_comments["comment"] as $key=>$_line) {
  if ($_line->signee) {
    unset($prescription->_ref_lines_med_comments["comment"][$key]);
  }
}

if ($prescription->_ref_lines_elements_comments) {
  foreach ($prescription->_ref_lines_elements_comments as $chap_key=>&$chapitre) {
    foreach ($chapitre as &$cat) {
      foreach ($cat as &$_elements) {
        foreach ($_elements as $element_key=>$_line) {
          if ($_line->signee) {
            unset($_elements[$element_key]);
            $prescription->_counts_by_chapitre[$chap_key] --;
          }
        }
      }
    }
  }
}
foreach ($prescription->_ref_prescription_line_mixes as $key=>$_line_mix) {
  if ($_line_mix->signature_prat) {
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
$smarty->assign("pratSel_id"     , $pratSel_id);
$smarty->assign("praticien_id"   , $praticien_id);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("checked_lines_tab"  , $checked_lines_tab);
$smarty->assign("sejour"         , $prescription->_ref_object);
$smarty->assign("now", mbDate());
$smarty->display("inc_select_lines.tpl");

?>