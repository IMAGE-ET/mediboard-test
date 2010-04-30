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
$plage_vac = new CPlageVacances();
$plage_vac->user_id = $user_id;
$plages_vac = $plage_vac->loadMatchingList();
foreach($plages_vac as $_plage) {
	$_plage->loadFwdRef("replacer_id");
	$replacer =& $_plage->_fwd["replacer_id"];
	$replacer->loadRefFunction();
}

$new_plagevac = new CPlageVacances();

$plage_id = CValue::get("plage_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",         $user);
$smarty->assign("plages_vac",   $plages_vac);
$smarty->assign("new_plagevac", $new_plagevac);
$smarty->assign("plage_id",     $plage_id);
$smarty->display("inc_liste_plages_vac.tpl");
?>