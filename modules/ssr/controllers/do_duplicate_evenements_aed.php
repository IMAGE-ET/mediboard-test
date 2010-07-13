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
  
	$evenement_ssr->_id = "";
	$evenement_ssr->realise = 0;
	$evenement_ssr->debut = mbDateTime("7 days", $evenement_ssr->debut);
	if($evenement_ssr->seance_collective_id){
    CAppUI::displayMsg("Impossible de dupliquer des evenements qui sont dans des seances collectives", "CEvenementSSR-msg-store");
	} else {
	  $msg = $evenement_ssr->store();
	  CAppUI::displayMsg($msg, "CEvenementSSR-msg-store");
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>