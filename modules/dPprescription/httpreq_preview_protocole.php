<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$protocole_id = mbGetValueFromGetOrSession("protocole_id");
$_entree      = mbGetValueFromGetOrSession("_entree");
$_sortie      = mbGetValueFromGetOrSession("_sortie");
$_datetime    = mbGetValueFromGetOrSession("_datetime");

$borne_min = mbGetValueFromGetOrSession("borne_min");
$borne_max = mbGetValueFromGetOrSession("borne_max");

// Chargement des categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Creation du tableau de dates
$dates = array();
if($_entree && $_sortie && $_datetime){
	$date = $_entree;
	while($date < $_sortie){
	  $dates[] = $date;
	  $date = mbDate("+ 1 DAY", $date);
	}
}
$prescription = new CPrescription();

$tabHours = array();
$heures = array();
$list_hours = range(0,23);
$hours = explode("|",CAppUI::conf("dPprescription CPrisePosologie heures_prise"));
sort($hours); 
$last_hour_in_array = reset($hours); 
foreach($list_hours as &$hour){
  $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
  if(in_array($hour, $hours)){
    $last_hour_in_array = $hour;
  }
  $heures[$hour] = $last_hour_in_array;
}

if($_entree && $_sortie && $_datetime){
  // Chargement de la prescription
  $prescription = $prescription->applyProtocole($protocole_id, null, null, null, $_entree, $_sortie, $_datetime);
  
  foreach($prescription->_ref_prescription_lines as &$line){
    $line->updateFormFields();
  }
  foreach($prescription->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
	  foreach($elements_chap as $name_cat => $elements_cat){
	    foreach($elements_cat as &$_elements){
	 	    foreach($_elements as &$_line_element){
	 	      $_line_element->updateFormFields();  
	 	    }
	    }
	  }
  }
  $types = array("med", "elt");
  foreach($types as $type){
    $prescription->_prises[$type] = array();
    $prescription->_lines[$type] = array();
    $prescription->_intitule_prise[$type] = array();
  }
  // Calcul du plan de soin 
  foreach($dates as $_date){
    foreach($types as $type){
      $prescription->_list_prises[$type][$_date] = array();
    }
    $prescription->calculPlanSoin($_date, 1, $heures);
    foreach($hours as $_hour){
  	  $tabHours[$_date]["$_date $_hour:00:00"] = $_hour;
    }
  }  
}

// Remplissage des filter fields
$operation = new COperation();
$operation->_datetime = $_datetime;

$sejour = new CSejour();
$sejour->_entree = $_entree;
$sejour->_sortie = $_sortie;


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("dates", $dates);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("operation", $operation);
$smarty->assign("sejour", $sejour);
$smarty->assign("last_log", new CUserLog());
$smarty->assign("pharmacien", new CMediusers());
$smarty->assign("categories", $categories);
$smarty->assign("patient", new CPatient());
$smarty->display("inc_preview_protocole.tpl");

?>