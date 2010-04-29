<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkRead();

$date    = CValue::getOrSession("date", mbDate());
$kine_id = CValue::getOrSession("kine_id");
$surveillance = CValue::getOrSession("surveillance");
$sejour_id = CValue::getOrSession("sejour_id");

$kine = new CMediusers();
$kine->load($kine_id);

$planning = new CPlanningWeek($date);
if($surveillance){
  $planning->title = "Planning de surveillance du technicien '$kine->_view'";
} else {
  $planning->title = "Planning du technicien '$kine->_view'";	
}

$planning->guid = $kine->_guid;

$date_min = reset(array_keys($planning->days));
$date_max = end(array_keys($planning->days));

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where["debut"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where["therapeute_id"] = " = '$kine->_id'";
$where["equipement_id"] = $surveillance ? " IS NOT NULL" : " IS NULL";
$evenements = $evenement_ssr->loadList($where);

foreach($evenements as $_evenement){
	$_evenement->loadRefElementPrescription();
	$important = ($_evenement->sejour_id == $sejour_id);
	$_evenement->loadRefSejour();
	$_evenement->_ref_sejour->loadRefPatient();
	$title = $_evenement->_ref_sejour->_ref_patient->_view;
	$element_prescription =& $_evenement->_ref_element_prescription;
  $color = $element_prescription->_color ? "#".$element_prescription->_color : null;
	$planning->addEvent(new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $title, $color, $important, $element_prescription->_guid));
}
$planning->addEvent(new CPlanningEvent(null, mbDateTime(), null, null, "red"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");

?>