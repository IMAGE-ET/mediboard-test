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

// Initialisation du planning
$date = CValue::getOrSession("date", mbDate());
$nb_days_planning = $sejour->getNbJourPlanning($date);

$planning = new CPlanningWeek($date, $sejour->entree, $sejour->sortie, $nb_days_planning, $selectable);
$planning->title = "Planning du patient '$patient->_view'";
$planning->guid = $sejour->_guid;

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where = array();
$where["sejour_id"] = " = '$sejour->_id'";
$where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
$evenements = $evenement_ssr->loadList($where);

foreach($evenements as $_evenement){
	$_evenement->loadRefsActesCdARR();
  $codes = count($_evenement->_ref_actes_cdarr) ? join(" - ", $_evenement->_ref_actes_cdarr) : '';
  
	$_evenement->loadRefElementPrescription();
	$element_prescription =& $_evenement->_ref_element_prescription;
  $element_prescription->loadRefCategory();
	$category_prescription =& $element_prescription->_ref_category_prescription;
  $title = $_evenement->_ref_element_prescription->_view ." - ".$codes;
	$color = $element_prescription->_color ? "#".$element_prescription->_color : null;
	$class_evt = $_evenement->equipement_id ? "equipement" : "kine";

  $css_classes = array($element_prescription->_guid, 
                       $category_prescription->_guid, 
                       $class_evt);
  $event = new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $title, $color, true, $css_classes);
  $event->draggable = true;
	$planning->addEvent($event);
}
$planning->addEvent(new CPlanningEvent(null, mbDateTime(),null, null, "red"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_planning_sejour.tpl");
