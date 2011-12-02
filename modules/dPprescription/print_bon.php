<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$sel_chapitre = CValue::get("sel_chapitre");
$debut = CValue::get("debut", mbDate());
$print = CValue::get("print", "0");
$list_bons = CValue::get("list_bons");

// Recuperation des bons à imprimer
$_list_bons = array();
if(count($list_bons)){
  foreach($list_bons as $_key_bon){
    $explode_bon = explode("-", $_key_bon);
    $line_bon = $explode_bon[0];
    $hour_bon = $explode_bon[1];
    $_list_bons[$line_bon][] = $hour_bon;
  }
}

$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsLinesElementByCat("1");

$prescription->calculPlanSoin(array($debut));

$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadRefConstantesMedicales();
$patient->loadIPP();
$sejour =& $prescription->_ref_object;
$sejour->loadNDA();

$sejour->loadRefCurrAffectation($debut);
$sejour->loadRefsOperations();
$sejour->_ref_last_operation->loadRefPlageOp();
$sejour->_ref_last_operation->loadExtCodesCCAM();
$bons = array();
$all_bons = array();
$lines = array();

// Liste des chapitres concernés par l'impression des bons
$chapitres = array("anapath", "biologie", "imagerie", "consult", "kine");

// Stockage des bons
if(count($prescription->_ref_lines_elt_for_plan)){
  foreach($prescription->_ref_lines_elt_for_plan as $_name_chap => $lines_by_cat){
    if(!in_array($_name_chap, $chapitres) || ($sel_chapitre && $sel_chapitre != $_name_chap)){
      continue;
    }
    foreach($lines_by_cat as $_name_cat => $_lines){
      foreach($_lines as $lines_by_unite){
        foreach($lines_by_unite as $_line){
          if(is_array($_line->_quantity_by_date)){
            foreach($_line->_quantity_by_date as $unite => $_quantity_by_date){
              foreach($_quantity_by_date as $_date => $quantites){
                foreach($quantites as $_quantites){
                  if(is_array($_quantites)){  
                    foreach($_quantites as $_hour => $_quantite){
                    	$print_bon = false;
                      // Si le bon n'a pas ete selectionné
                      if(array_key_exists($_line->_id, $_list_bons) && in_array($_hour, $_list_bons[$_line->_id])){
                        $print_bon = true;
                      }

                      if(isset($_line->_administrations[$unite][$_date][$_hour]["quantite_planifiee"])){
                        $quantite = $_line->_administrations[$unite][$_date][$_hour]["quantite_planifiee"];
                        if($print_bon){
                          @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["quantite"] += $quantite;
													if(isset($_quantite["urgence"]) && $_quantite["urgence"]){
		                        @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["urgence"] = true;
		                      }
                        }
                        @$all_bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["quantite"] += $quantite;
                      }
                      
                      if(isset($_quantite["total"]) && $_quantite["total"]){
                        if($print_bon){                      
                          @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["quantite"] += $_quantite["total"];
													if(isset($_quantite["urgence"]) && $_quantite["urgence"]){
		                        @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["urgence"] = true;
		                      }
                        }
                        @$all_bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["quantite"] += $_quantite["total"];
                      }

                      if(!array_key_exists($_line->_id, $lines)){
                        $lines[$_line->_id] = $_line;
                      }
                    
                    }
                  }
                }
              }
            }
          }
          if(is_array($_line->_administrations)){
            foreach($_line->_administrations as $unite_prise => &$administrations_by_unite){
              foreach($administrations_by_unite as $_date => &$administrations_by_date){
                foreach($administrations_by_date as $_hour => &$administrations_by_hour){
                  if(is_numeric($_hour)){
                    $print_bon = false;
                    // Si le bon ne fait pas parti de ceux selectionnes
                    if(array_key_exists($_line->_id, $_list_bons) && in_array($_hour, $_list_bons[$_line->_id])){
                     $print_bon = true;
                    }
                    
                    $quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
                    if($quantite_planifiee){
                      if($print_bon){
                        @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["quantite"] += $quantite_planifiee;
                      }
                      @$all_bons[$_name_chap][$_hour][$_name_cat][$_line->_id]["quantite"] += $quantite_planifiee;
                    }
                    
                    if(!array_key_exists($_line->_id, $lines)){
                      $lines[$_line->_id] = $_line;
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}

// Tri par heures
foreach($bons as $_chap => &$_bons){
  ksort($_bons);
}

// Tri par heures
foreach($all_bons as $_chap => &$_all_bons){
  ksort($_all_bons);
}

$ex_objects = array();

if ($print) {
  $ex_class = new CExClass;
  $ex_class->host_class = 'CPrescriptionLineElement';
  //$ex_class->event      = 'prescription';
  
  $ex_classes = $ex_class->loadMatchingList();
  CExObject::$_multiple_load = true;
  //CExClassField::$_load_lite = true; // ne charge pas les noms des champs
  
  foreach($lines as $_line) {
    $ex_objects[$_line->_guid] = array();
  }
  
  foreach($ex_classes as $_ex_class) {
    $ex_object = new CExObject;
    $ex_object->_ex_class_id = $_ex_class->_id;
    $ex_object->setExClass();
      
    foreach($lines as $_line) {
      $where = array(
        "object_class" => "='$_line->_class'",
        "object_id"    => "='$_line->_id'",
      );
      
      $_ex_objects = $ex_object->loadList($where, "ex_object_id DESC", 1);
      $_ex_object = reset($_ex_objects);
      
      if ($_ex_object && $_ex_object->_id) {
        CExClassField::$_load_lite = false;
          $_ex_object->_ex_class_id = $_ex_class->_id;
          $_ex_object->setExClass();
          $_ex_object->load();
          $_ex_object->loadTargetObject();
          $_ex_object->_ref_object->loadComplete();
        CExClassField::$_load_lite = true;
        
        $ex_objects[$_line->_guid][] = $_ex_object;
      }
    }
  }
}

// Creation d'un tableau des affectations pour la date courante
$prescription->_ref_object->loadRefsAffectations();

foreach($prescription->_ref_object->_ref_affectations as $_affectation) {
  $_affectation->loadView();
}

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

$filter_line = new CPrescriptionLineElement();
$filter_line->debut = $debut;

$smarty = new CSmartyDP;
$smarty->assign("all_bons", $all_bons);
$smarty->assign("bons", $bons);
$smarty->assign("lines", $lines);
$smarty->assign("debut", $debut);
$smarty->assign("prescription", $prescription);
$smarty->assign("nodebug", true);
$smarty->assign("sel_chapitre", $sel_chapitre);
$smarty->assign("chapitres", $chapitres);
$smarty->assign("filter_line", $filter_line);
$smarty->assign("categories", $categories);
$smarty->assign("debut", $debut);
$smarty->assign("print", $print);
$smarty->assign("list_bons", $list_bons);
$smarty->assign("ex_objects", $ex_objects);
$smarty->display("print_bon.tpl");

?>