<?php /* $Id: vw_bilan_prescription.php 6159 2009-04-23 08:54:24Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$categories_id = CValue::getOrSession("categories_id");
$date          = CValue::getOrSession("date");
$date_max      = mbDate("+ 1 DAY", $date);
$service_id    = CValue::getOrSession("service_id");
$nb_decalage   = CValue::get("nb_decalage", 2);
$mode_dossier  = CValue::get("mode_dossier", "administration");
/*
 * Code a supprimer
 */

$configs = CConfigService::getAllFor("none");

$matin = range($configs["Borne matin min"], $configs["Borne matin max"]);
$soir = range($configs["Borne soir min"], $configs["Borne soir max"]);
$nuit_soir = range($configs["Borne nuit min"], 23);
$nuit_matin = range(00, $configs["Borne nuit max"]);

foreach($matin as &$_hour_matin){
  $_hour_matin = str_pad($_hour_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($soir as &$_soir_matin){
  $_soir_matin = str_pad($_soir_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($nuit_soir as &$_hour_nuit_soir){
  $nuit[] = str_pad($_hour_nuit_soir, 2, "0", STR_PAD_LEFT);
}
foreach($nuit_matin as &$_hour_nuit_matin){
  $nuit[] = str_pad($_hour_nuit_matin, 2, "0", STR_PAD_LEFT);
}

// Recuperation de l'heure courante
$time = mbTransformTime(null,null,"%H");

// Construction de la structure de date à parcourir dans le tpl
if(in_array($time, $matin)){
  $dates = array(mbDate("- 1 DAY", $date) => array("soir" => $soir, "nuit" => $nuit), 
                 $date                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit));
}
if(in_array($time, $soir)){
  $dates = array(mbDate("- 1 DAY", $date) => array("nuit" => $nuit),
                 $date                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit),
                 mbDate("+ 1 DAY", $date) => array("matin" => $matin));
}
if(in_array($time, $nuit)){
  $dates = array($date                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit), 
                 mbDate("+ 1 DAY", $date) => array("matin" => $matin, "soir" => $soir));
}



$bornes_composition_dossier = array();
$composition_dossier = array();
foreach($dates as $curr_date => $_date){
  foreach($_date as $moment_journee => $_hours){
    $composition_dossier[] = "$curr_date-$moment_journee";
    $bornes_composition_dossier["$curr_date-$moment_journee"]["min"] = "$curr_date ".reset($_hours).":00:00";
    foreach($_hours as $_hour){ 
      $date_reelle = $curr_date;
      if($moment_journee == "nuit" && $_hour < "12:00:00"){
        $date_reelle = mbDate("+ 1 DAY", $curr_date);
      }
      $_dates[$date_reelle] = $date_reelle;
      $tabHours[$curr_date][$moment_journee][$date_reelle]["$_hour:00:00"] = $_hour;
    }
    $bornes_composition_dossier["$curr_date-$moment_journee"]["max"] = "$date_reelle ".end($_hours).":59:59";
  }
}

//-- Fin du code a supprimer 
  
// Chargement des lignes de prescription
$line = new CPrescriptionLineElement();

$ljoin = array();
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id AND prescription.type = 'sejour'";
$ljoin["sejour"] = "sejour.sejour_id = prescription.object_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
  
$where = array();
$where["element_prescription_id"] =  CSQLDataSource::prepareIn($categories_id);
$where[] = "'$date' <= sejour.sortie && '$date_max' >= sejour.entree";
$where["service.service_id"] = " = '$service_id'";

$lines = $line->loadList($where, null, null, null, $ljoin);

// Chargement du patient pour chaque sejour
$sejours       = CMbArray::pluck($lines, "_ref_prescription", "_ref_object");
$patients      = CMbObject::massLoadFwdRef($sejours, "patient_id");

$prescriptions = array();


foreach($lines as $_line_element){
	if(!in_array($_line_element->prescription_id, $prescriptions)){
		$prescriptions[$_line_element->prescription_id] = $_line_element->_ref_prescription;
	}
  $prescription =& $prescriptions[$_line_element->prescription_id];
  $prescription->loadRefPatient();
	$_line_element->loadRefsPrises();
	$_line_element->loadRefLogSignee();
	$_line_element->_ref_praticien->loadRefFunction();
	$category = $_line_element->_ref_element_prescription->_ref_category_prescription;
	$name_chap = $category->chapitre;
	$name_cat = $category->_id;

  // Chargement des planificatoins systemes	
  $planif = new CPlanificationSysteme();
  $planif->object_id = $_line_element->_id;
  $planif->object_class = $_line_element->_class_name;
  if(!$planif->countMatchingList()){
    $_line_element->calculPlanifSysteme();
  }
	
  // Chargement des administrations et des transmissions
  foreach($_dates as $date){
    $_line_element->calculAdministrations($date);
  }

  foreach($_dates as $date){
    // Pre-remplissage des prises prevues dans le dossier de soin
    if(($date >= $_line_element->debut && $date <= mbDate($_line_element->_fin_reelle))){
      // Si aucune prise  
      if ((count($_line_element->_ref_prises) < 1) && (!isset($prescription->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"]))){
        $prescription->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
       
			}
      $_line_element->calculPrises($prescription, $date, $name_chap, $name_cat, 1, CAppUI::conf("dPprescription CPrescription manual_planif"));
		}
  }

  // Suppression des prises prevues replanifiées
  $_line_element->removePrisesPlanif();	
}

$nb_lines_element = 0;
foreach ($prescriptions as $_prescription){
	if($_prescription->_ref_lines_elt_for_plan){
		foreach ($_prescription->_ref_lines_elt_for_plan as $_elements_by_chap){
			foreach ($_elements_by_chap as $elements_cat){
				foreach ($elements_cat as $_element){
					$nb_lines_element += count($_element);
				}
			}
		}
	}
}
  
// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("patients", $patients);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("prescriptions", $prescriptions);
$smarty->assign("composition_dossier", $composition_dossier);
$smarty->assign("bornes_composition_dossier", $bornes_composition_dossier);
$smarty->assign("categories", $categories);

$count_colspan = CAppUI::conf("dPprescription CPrescription manual_planif") ? 1 : 0;
$smarty->assign("tabHours", $tabHours);
$smarty->assign("count_matin"         , count($matin)+2+$count_colspan);
$smarty->assign("count_soir"          , count($soir)+2+$count_colspan);
$smarty->assign("count_nuit"          , count($nuit)+2+$count_colspan);
$smarty->assign("operations", array());
$smarty->assign("nb_decalage", $nb_decalage);
$smarty->assign("composition_dossier", $composition_dossier);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("nb_lines_element", $nb_lines_element);
$smarty->assign("now", mbDateTime());
$smarty->assign("date", $date);
$smarty->assign("move_dossier_soin", false);
 
$smarty->display('inc_vw_content_plan_soins_service.tpl');

?>