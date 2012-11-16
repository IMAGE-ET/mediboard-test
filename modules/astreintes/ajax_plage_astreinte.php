<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

//CCanDo::checkRead();

$user_id = CValue::getorSession("user_id");
$user = new CMediusers();
$user->load($user_id);

// Plages d'astreinte pour l'utilisateur
$plage_astreinte = new CPlageAstreinte();
$plage_astreinte->user_id = $user_id;
$plages_astreinte = $plage_astreinte->loadMatchingList("date_debut");

$new_plageastreinte = new CPlageAstreinte();

$plage_id = CValue::get("plage_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",         $user);
$smarty->assign("plages_astreinte",   $plages_astreinte);
$smarty->assign("new_plageastreinte", $new_plageastreinte);
$smarty->assign("plage_id",     $plage_id);
$smarty->display("inc_liste_plages_astreinte.tpl");
?>