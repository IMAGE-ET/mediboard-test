<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


$token_elts = CValue::post("token_elts");
$del        = CValue::post("del", 0);

$elts_id = explode("|", $token_elts);
foreach($elts_id as $_elt_id){
	if($del){
		$evenement_ssr = new CEvenementSSR();
		$evenement_ssr->load($_elt_id);
		$msg = $evenement_ssr->delete();
		CAppUI::displayMsg($msg, "CEvenementSSR-msg-delete");
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>