<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$token_evts = CValue::getOrSession("token_evts");

$_evenements = array();
if($token_evts){
  $_evenements = explode("|", $token_evts);
}

$actes = array();
$count_actes = array();
$evenements = array();

foreach($_evenements as $_evenement_id){
	$evenement = new CEvenementSSR();
  $evenement->load($_evenement_id);
	
	if($evenement->seance_collective_id){
	  // Recuperation des informations de la seance collective
		$evenement->loadRefSeanceCollective();
		$evenement->debut = $evenement->_ref_seance_collective->debut;
		$evenement->duree = $evenement->_ref_seance_collective->duree;
	}
	
  $evenement->loadRefSejour();
  $evenement->_ref_sejour->loadRefPatient();
  $evenement->loadRefPrescriptionLineElement();

  // Chargement des actes cdarrs de l'evenement
  $evenement->loadRefsActesCdARR();
	foreach($evenement->_ref_actes_cdarr as $_acte_cdarr){
		$actes[$_acte_cdarr->code] = $_acte_cdarr->code;
		if(!isset($count_actes[$_acte_cdarr->code])){
			$count_actes[$_acte_cdarr->code] = 0;
		}
		$count_actes[$_acte_cdarr->code]++;
	}
	
	// Chargement des actes cdarrs possibles pour l'evenement
	$element_prescription =& $evenement->_ref_prescription_line_element->_ref_element_prescription;
	$element_prescription->loadRefsCdarrs();
	
	foreach($element_prescription->_ref_cdarrs as $element_to_acte_cdarr){
    $actes[$element_to_acte_cdarr->code] = $element_to_acte_cdarr->code;
  }
  $evenements[$evenement->_id] = $evenement;
}

ksort($actes);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("token_evts", $token_evts);
$smarty->assign("evenements", $evenements);
$smarty->assign("actes", $actes);
$smarty->assign("count_actes", $count_actes);
$smarty->display("inc_vw_modal_evts_modif.tpl");

?>