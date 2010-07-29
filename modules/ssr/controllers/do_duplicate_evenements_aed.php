<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$token_elts  = CValue::post("token_elts");
$period      = CValue::post("period");


$elts_id = explode("|", $token_elts);
foreach($elts_id as $_elt_id){
  $evenement = new CEvenementSSR();
  $evenement->load($_elt_id);
  $evenement->loadRefsActesCdARR();

  // Duplication de l'événement  
	$evenement->_id = "";
	$evenement->realise = 0;
	$evenement->debut = mbDateTime("+1 $period", $evenement->debut);

  // Cas des séances collectives
	if ($evenement->seance_collective_id){
    CAppUI::displayMsg("Impossible de dupliquer des événements qui sont dans des seances collectives", "CEvenementSSR-msg-create");
		continue;
	} 

  // Autres rééducateurs
	global $can;
	if ($evenement->therapeute_id !=  CAppUI::$instance->user_id && !$can->admin) {
    CAppUI::displayMsg("Impossible de dupliquer les événements d'un autre rééducateur", "CEvenementSSR-msg-create");
    continue;
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

echo CAppUI::getMsg();
CApp::rip();

?>