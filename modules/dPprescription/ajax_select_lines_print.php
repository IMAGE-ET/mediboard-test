<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id    = CValue::get("praticien_id");
$prescription_id = CValue::get("prescription_id");

$prescription = new CPrescription;
$prescription->load($prescription_id);

$praticien = new CMediusers;
$praticien->load($praticien_id);

$all_lines = array();

// Chargement des lignes de prescriptions
$prescription->loadRefsLinesMedComments("1", "debut ASC");
$prescription->loadRefsLinesElementsComments("0", "1","","debut ASC");

// Chargement des prescription_line_mixes
$prescription->loadRefsPrescriptionLineMixes("", 0, 1, "");
foreach($prescription->_ref_prescription_line_mixes as $curr_prescription_line_mix){
  $curr_prescription_line_mix->loadRefsLines();
}

if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi')) {
  $prescription->loadRefsLinesDMI($operation_id);
}

// Parcours des prescription_line_mixes
foreach ($prescription->_ref_prescription_line_mixes as $_prescription_line_mix) {
  $_prescription_line_mix->loadRefPraticien();
  $_prescription_line_mix->loadRefsVariantes();
  foreach ($_prescription_line_mix->_ref_variantes as $_subst_by_chap) {
    foreach ($_subst_by_chap as &$_subst_perf) {
      $_subst_perf->loadRefPraticien();
      if ($_subst_perf instanceof CPrescriptionLineMix) {
        $_subst_perf->loadRefsLines();
      }
      else {
        $_subst_perf->loadRefsPrises();
      }
    }
  }    
  if ($prescription->object_id && $praticien->_id && $_prescription_line_mix->praticien_id != $praticien->_id) {
    continue;
  } 
  if ($_prescription_line_mix->next_line_id) {
    continue;
  }
  if (!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$_prescription_line_mix->signature_prat && $prescription->object_id) {
    continue;
  }

  $all_lines["medicaments"][] = $_prescription_line_mix;
}

if(count($prescription->_ref_lines_elements_comments)){
  foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
    foreach($chap_element as $name_cat => $cat_element){
      foreach($cat_element as $type => $elements){
        foreach($elements as $element){
          if($prescription->object_id && $praticien->_id && $element->praticien_id != $praticien->_id){
            continue;
          } 
          if($element->child_id){
            continue;
          }
          if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$element->signee && $prescription->object_id){
            continue;
          }
          $element->loadRefsFwd();
          $executant = "aucun";
          if($element->_ref_executant){
            $executant = $element->_ref_executant->_guid;
          }
          
          $all_lines[$name_chap][] = $element;
        }
      }
    }
  }
}

foreach($prescription->_ref_lines_med_comments as $key => $lines_medicament_type){
  foreach($lines_medicament_type as $line_medicament){
    $line_medicament->loadRefsFwd();
    if(!$prescription->object_id){
      if($line_medicament instanceof CPrescriptionLineMedicament){
        $line_medicament->loadRefsVariantes();
        foreach($line_medicament->_ref_variantes as $_subst_by_chap){
          foreach($_subst_by_chap as $_subst_line){
            if($_subst_line instanceof CPrescriptionLineMedicament){
              $_subst_line->loadRefsPrises();
            } else {
              $_subst_line->loadRefsLines();
            }
            $_subst_line->loadRefsFwd();
          }
        }
      }
    }
    if($prescription->object_id && $praticien->_id && $line_medicament->praticien_id != $praticien->_id){
      continue;
    } 
    if($line_medicament->child_id){
      continue;
    }
    if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$line_medicament->signee && $prescription->object_id){
      continue;
    }
    
    $all_lines["medicaments"][] = $line_medicament;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("all_lines", $all_lines);

$smarty->display("inc_select_lines_print.tpl");
?>