<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$token_field_evts = CValue::getOrSession("token_field_evts");

$sejours = array();
$events = array();
$_evenements = $token_field_evts ? explode("|", $token_field_evts) : array();
foreach($_evenements as $_evenement_id){
	$evenement = new CEvenementSSR();
	$evenement->load($_evenement_id);
	
	if($evenement->sejour_id){
		$events[$evenement->_id] = $evenement;
	} 
	else {
		$evenement->loadRefsEvenementsSeance();
		foreach($evenement->_ref_evenements_seance as $_evt_seance){
			$_evt_seance->debut = $evenement->debut;
			$_evt_seance->duree = $evenement->duree;
      
			$events[$_evt_seance->_id] = $_evt_seance;
		}
	}
}


$count_zero_actes = 0;
$evenements = array();
foreach($events as $_event){
  $_event->loadRefsActesCdarr();
  $_event->loadRefEquipement();
	if (!count($_event->_ref_actes_cdarr)) {
		$count_zero_actes++;
	}

  $_event->loadRefSejour();
  $_event->_ref_sejour->loadRefPatient();
  $sejours[$_event->sejour_id] = $_event->_ref_sejour;
  $_event->loadRefPrescriptionLineElement();
  $element_prescription_id = $_event->_ref_prescription_line_element->element_prescription_id;
  $date_debut = mbDate($_event->debut);
  $evenements[$_event->sejour_id][$element_prescription_id.$date_debut][$_event->_id] = $_event;
}

foreach($evenements as &$evenements_by_sejour){
  ksort($evenements_by_sejour);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenements", $evenements);
$smarty->assign("sejours", $sejours);
$smarty->assign("count_zero_actes", $count_zero_actes);
$smarty->display("inc_vw_modal_evenements.tpl");

?>