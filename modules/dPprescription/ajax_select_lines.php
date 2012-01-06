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
$tp = CValue::get("tp", 0);

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
$count_past_lines = 0;
$current_date = mbDate();
$prescription = new CPrescription;

$mode = $tp ? "tp" : "validation";
if(!$pack_protocole && !$tp){
  $mode	= "duplicate";
}

if ($protocole_id || (!$pack_id && !$tp)) {
  $prescription->load($prescription_id);
  $prescription->loadRefsLinesMedComments("1", "", $protocole_id);
  $prescription->loadRefsLinesElementsComments("0", "1", "", "", $protocole_id);
  $prescription->loadRefsPrescriptionLineMixes("", 1, 1, $protocole_id);
  $prescription->countLinesMedsElements(null, null, $protocole_id);
  	
  // On instancie le protocole utilisé pour récupérer
  // le champ checked_lines (lignes cochées dans la modale)
  $protocole = new CPrescription;
  $protocole->load($protocole_id);
  $checked_lines_tab[$protocole_id] = $protocole->checked_lines;
}
elseif ($pack_id) {
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
      
      $prescription->loadRefsLinesElementsComments("0", "1", "", "", $protocole_id);
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
} elseif ($tp){
	// Chargement des traitements personnels dans la prescription
	$_tp_med = new CPrescriptionLineMedicament();
	$_tp_med->traitement_personnel = 1;
	$_tp_med->prescription_id = $prescription_id;
	$_tp_med->signee = 0;
	$traitements_perso = $_tp_med->loadMatchingList();
	
	foreach($traitements_perso as $_med_tp){
		$_med_tp->loadRefsPrises();
	}
	
	$prescription->_counts_by_chapitre["med"] = count($traitements_perso);
	 
	$prescription->_ref_lines_med_comments["med"] = $traitements_perso;
	$prescription->_ref_lines_med_comments["comment"] = array();
	$prescription->_ref_lines_elements_comments = array();
  $prescription->_ref_prescription_line_mixes = array();
}

// Suppression des lignes signées
// Check par rapport à la date courante
foreach ($prescription->_ref_lines_med_comments["med"] as $key=>$_line) {
  if ($_line->signee) {
    unset($prescription->_ref_lines_med_comments["med"][$key]);
    $prescription->_counts_by_chapitre["med"] --;
  }
  else {
    if ($_line->debut && $_line->debut < $current_date) {
      $count_past_lines++;
      $_line->_is_past = true;
    }
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
          else {
            if ($_line->debut && $_line->debut < $current_date) {
              $_line->_is_past = true;
              $count_past_lines++;
            }
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
    if ($_line_mix->date_debut < $current_date) {
      $_line->_is_past = true;
      $count_past_lines++;
    }
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
$smarty->assign("count_past_lines", $count_past_lines);
$smarty->assign("mode"         , $mode);

$smarty->display("inc_select_lines.tpl");

?>