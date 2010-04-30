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

$patient = $sejour->_ref_patient;

// Initialisation du planning
$date = CValue::getOrSession("date", mbDate());
$planning = new CPlanningWeek($date, $sejour->entree, $sejour->sortie, true);
$planning->title = "Planning du patient '$patient->_view'";
$planning->guid = $sejour->_guid;

$date_min = reset(array_keys($planning->days));
$date_max = end(array_keys($planning->days));

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where["sejour_id"] = " = '$sejour->_id'";
$where["debut"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$evenements = $evenement_ssr->loadList($where);

foreach($evenements as $_evenement){
	$_evenement->loadRefElementPrescription();
	$title = $_evenement->_ref_element_prescription->_view ." - ".$_evenement->code;
	$element_prescription =& $_evenement->_ref_element_prescription;
  $color = $element_prescription->_color ? "#".$element_prescription->_color : null;
	$planning->addEvent(new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $title, $color, true, $element_prescription->_guid));
}
$planning->addEvent(new CPlanningEvent(null, mbDateTime(), null, null, "red"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");
