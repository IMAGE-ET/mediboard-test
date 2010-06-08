<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
//$can->needsRead();

$user_id = CValue::getorSession("user_id");
$user = new CMediusers();
$user->load($user_id);

// Plages de congés pour l'utilisateur
$plage_conge = new CPlageConge();
$plage_conge->user_id = $user_id;
$plages_conge = $plage_conge->loadMatchingList();
foreach($plages_conge as $_plage) {
	$_plage->loadFwdRef("replacer_id");
	$replacer =& $_plage->_fwd["replacer_id"];
	$replacer->loadRefFunction();
}

$new_plageconge = new CPlageConge();

$plage_id = CValue::get("plage_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",         $user);
$smarty->assign("plages_conge",   $plages_conge);
$smarty->assign("new_plageconge", $new_plageconge);
$smarty->assign("plage_id",     $plage_id);
$smarty->display("inc_liste_plages_conge.tpl");
?>