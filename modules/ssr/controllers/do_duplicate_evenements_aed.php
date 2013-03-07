<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$event_ids = CValue::post("event_ids");
$period    = CValue::post("period");
$days      = array("");

// Propagation aux autres jours
if (CValue::post("propagate")) {
	$days = array();
	
  // Ugly hack du m_post
  global $m;
  $m = $m_post;
	
  $date = CValue::getOrSession("date", CMbDT::date());
  $monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
	foreach(CValue::post("_days") as $_number) {
    $days[] = CMbDT::date("+$_number DAYS", $monday);
	}
}

$elts_id = explode("|", $event_ids);

foreach($days as $day) {
	foreach($elts_id as $_elt_id){
	  $evenement = new CEvenementSSR();
	  $evenement->load($_elt_id);
	  $evenement->loadRefsActesCdARR();
	
	  // Duplication de l'événement  
	  $evenement->_id = "";
    $evenement->realise = 0;
    $evenement->annule = 0;
	  $evenement->debut = $day ? "$day ".CMbDT::time($evenement->debut) :  CMbDT::dateTime($period, $evenement->debut);
	
	  // Cas des séances collectives
	  if ($evenement->seance_collective_id){
	    CAppUI::displayMsg("Impossible de dupliquer des événements qui sont dans des seances collectives", "CEvenementSSR-msg-create");
	    continue;
	  } 
	
	  // Autres rééducateurs
	  global $can;
	  $user = CAppUI::$user;
	  $therapeute = $evenement->loadRefTherapeute();
	  if ($therapeute->function_id !=  $user->function_id && !$can->admin) {
	    CAppUI::displayMsg("Impossible de dupliquer les événements d'un rééducateur d'une autre spécialié", "CEvenementSSR-msg-create");
	    continue;
	  }
	
	  // Chargements préparatoire au transferts automatiques de rééducateurs  
	  $sejour = new CSejour;
	  $sejour->load($evenement->sejour_id);
	  $sejour->loadRefBilanSSR();
	  
	  $bilan =& $sejour->_ref_bilan_ssr;
	  $bilan->loadRefKineReferent();
	  
	  $referant =& $bilan->_ref_kine_referent;
	  $_day = CMbDT::date($evenement->debut);
	  $therapeute_id = $evenement->therapeute_id;
	  
	  // Transfert kiné référent => kiné remplaçant si disponible
	  if ($therapeute_id == $referant->_id) {
	    $conge = new CPlageConge();
	    $conge->loadFor($therapeute_id, $_day);
	    // Référent en congés
	    if ($conge->_id){
	      $replacement = new CReplacement();
	      $replacement->conge_id = $conge->_id;
	      $replacement->sejour_id = $sejour->_id;
	      $replacement->loadMatchingObject();
	      if ($replacement->_id) {
	        $evenement->therapeute_id = $replacement->replacer_id;
	      }
	    }
	  }
	
	  // Transfert kiné remplacant => kiné référant si présent
	  if ($sejour->isReplacer($therapeute_id)) {
	    $conge = new CPlageConge();
	    $conge->loadFor($referant->_id, $_day);
	    // Référent présent
	    if (!$conge->_id){
	      $evenement->therapeute_id = $referant->_id;
	    }
	  }
	  
	  $msg = $evenement->store();
	  CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
	
	  // Duplication des codes CdARR
	  if ($evenement->_id) {
	    foreach ($evenement->_ref_actes_cdarr as $_acte_cdarr) {
	      $_acte_cdarr->_id = "";
	      $_acte_cdarr->evenement_ssr_id = $evenement->_id;
	      $msg = $_acte_cdarr->store();
	      CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
	    }
	  }
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>