<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$token_evts  = CValue::post("token_evts");
$code        = CValue::post("code");
$checked     = CValue::post("checked");
$_evenements = explode("|", $token_evts);

foreach($_evenements as $_evenement_id){
	 $acte_cdarr = new CActeCdARR();
   $acte_cdarr->evenement_ssr_id = $_evenement_id;
   $acte_cdarr->code = $code;
   $acte_cdarr->loadMatchingObject();
		
	// ajout de l'acte a tous les evenements si on coche la checkbox ou si on clique dessus et que seulement certains evts sont checked
	if($checked){
		if(!$acte_cdarr->_id){
			$msg = $acte_cdarr->store();
			CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
		}
	}
	// Suppression de l'acte pour tous les evenements
	else {
    if($acte_cdarr->_id){
    	$msg = $acte_cdarr->delete();
      CAppUI::displayMsg($msg, "CActeCdARR-msg-delete");
		}
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>