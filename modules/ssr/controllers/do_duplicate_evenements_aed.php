<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$token_elts  = CValue::post("token_elts");

$elts_id = explode("|", $token_elts);
foreach($elts_id as $_elt_id){
  $evenement_ssr = new CEvenementSSR();
  $evenement_ssr->load($_elt_id);
  $evenement_ssr->loadRefsActesCdARR();

  // Duplication de l'événement  
	$evenement_ssr->_id = "";
	$evenement_ssr->realise = 0;
	$evenement_ssr->debut = mbDateTime("7 days", $evenement_ssr->debut);
	if ($evenement_ssr->seance_collective_id){
    CAppUI::displayMsg("Impossible de dupliquer des evenements qui sont dans des seances collectives", "CEvenementSSR-msg-store");
	} 
	else {
	  $msg = $evenement_ssr->store();
	  CAppUI::displayMsg($msg, "CEvenementSSR-msg-store");
	}

  // Duplication des codes CdARR
	if ($evenement_ssr->_id) {
		foreach ($evenement_ssr->_ref_actes_cdarr as $_acte_cdarr) {
		  $_acte_cdarr->_id = "";
		  $_acte_cdarr->evenement_ssr_id = $evenement_ssr->_id;
	    $msg = $_acte_cdarr->store();
	    CAppUI::displayMsg($msg, "CActeCdARR-msg-store");
		}
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>