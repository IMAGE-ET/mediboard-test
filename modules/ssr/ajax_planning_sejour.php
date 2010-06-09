<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

$selectable = CValue::get("selectable");
$patient = $sejour->_ref_patient;

$height = CValue::get("height");
$print = CValue::get("print");
$large = CValue::get("large");

// Initialisation du planning
$date = CValue::getOrSession("date", mbDate());
$nb_days_planning = $sejour->getNbJourPlanning($date);

$planning = new CPlanningWeek($date, $sejour->entree, $sejour->sortie, $nb_days_planning, $selectable, $height, $large);
$planning->title = "Planning du patient '$patient->_view'";
$planning->guid = $sejour->_guid;

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where = array();
$where["sejour_id"] = " = '$sejour->_id'";
$where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
$evenements = $evenement_ssr->loadList($where);

foreach($evenements as $_evenement){
	$_evenement->loadRefElementPrescription();
	$element_prescription =& $_evenement->_ref_element_prescription;
  $element_prescription->loadRefCategory();
	$category_prescription =& $element_prescription->_ref_category_prescription;
  $title = $category_prescription->_view;
	
	if($print){
		$_evenement->loadRefTherapeute();
		$_evenement->loadRefEquipement();
		$title .= " - ".$_evenement->_ref_therapeute->_view;
		$title .= $_evenement->equipement_id ? " - ".$_evenement->_ref_equipement->_view : '';
		$title .= $_evenement->remarque ? " - ".$_evenement->remarque : ''; 
	}
	
	$color = $element_prescription->_color ? "#".$element_prescription->_color : null;
	$class_evt = $_evenement->equipement_id ? "equipement" : "kine";

  $css_classes = array($element_prescription->_guid, 
                       $category_prescription->_guid);
	
	$css_classes[] = ($_evenement->realise && !$print) ? "realise" : $class_evt;
											 
  $event = new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $title, $color, true, $css_classes);
  $event->draggable = true;
	$planning->addEvent($event);
}

$planning->showNow();

// Alertes séjour
$total_evenement = array();
foreach($planning->days as $_day => $day) {
	if ($planning->isDayActive($_day)) {
	  $total_evenement[$_day]["duree"] = 0;
	  $total_evenement[$_day]["nb"] = 0;
	}
}

foreach($evenements as $_evenement){
  $total_evenement[mbDate($_evenement->debut)]["duree"] += $_evenement->duree;
  $total_evenement[mbDate($_evenement->debut)]["nb"]++;
}

foreach ($total_evenement as $_date => $_total_evt){
	$alerts = array();
	if ($_total_evt["duree"] < 120) {
		$alerts[] = "< 2h";
	}
	if($_total_evt["nb"] < 1){
    $alerts[] = "0 indiv. ";
  }
	if($count = count($alerts)) {
	  $color = ($count == 2) ? "#f88" : "#ff4";
    $planning->addDayLabel($_date, implode(" / ", $alerts) , null, $color);
  }
}


$sejour->loadRefReplacement();
if($sejour->_ref_replacement->_id){
	$replacement =& $sejour->_ref_replacement;
	$replacement->loadRefReplacer();
  $replacement->loadRefConge();
	$conge =& $sejour->_ref_replacement->_ref_conge;
	
	for ($day = $conge->date_debut; $day <= $conge->date_fin; $day = mbDate("+1 DAY", $day)) {
    $planning->addDayLabel($day, $sejour->_ref_replacement->_ref_replacer->_view);
  }	
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_planning_sejour.tpl");
