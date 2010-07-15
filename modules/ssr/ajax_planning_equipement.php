<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkRead();

$date = CValue::getOrSession("date", mbDate());
$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$equipement = new CEquipement;
$equipement->load(CValue::get("equipement_id"));

if ($equipement->visualisable) {
  $nb_days_planning = $sejour->getNbJourPlanning($date);
  $planning = new CPlanningWeek($date, null, null, $nb_days_planning, false, "auto", false, true);
  $planning->title = "Planning de l'équipement '$equipement->_view'";
  $planning->guid = $equipement->_guid;
  
  // Chargement des evenement SSR 
  $evenement_ssr = new CEvenementSSR();
  $where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
  $where["equipement_id"] = " = '$equipement->_id'";
  $evenements = $evenement_ssr->loadList($where);
  
  foreach($evenements as $_evenement){
  	$important = !$sejour_id || $_evenement->sejour_id == $sejour_id;
  	
  	$_evenement->loadRefPrescriptionLineElement();
  	$_evenement->loadRefSejour();
  	$_evenement->loadRefTherapeute();
    $_evenement->_ref_sejour->loadRefPatient();
  	$therapeute = $_evenement->_ref_therapeute;
    $patient =  $_evenement->_ref_sejour->_ref_patient;
    $title = ucfirst(strtolower($patient->nom))."  $therapeute->_shortview";
    $element_prescription =& $_evenement->_ref_prescription_line_element->_ref_element_prescription;
  	$color = $element_prescription->_color ? "#".$element_prescription->_color : null;
  
    $css_classes = array($element_prescription->_guid);
    $planning->addEvent(new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $title, $color, $important, $css_classes));
  }
  
  $planning->showNow();
  
  // Création du template
  $smarty = new CSmartyDP();
  $smarty->assign("planning", $planning);
  $smarty->display("inc_vw_week.tpl");
}
else {
  CAppUI::stepMessage(UI_MSG_WARNING, "L'équipement <strong>$equipement->_view</strong> n'est pas visualisable");
  CApp::rip();
}

?>