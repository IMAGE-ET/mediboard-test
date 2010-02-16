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

$plage_vac = new CPlageVacances();
$plage_vac->user_id = $user_id;

$plages_vac = array();
$plages_vac = $plage_vac->loadMatchingList();

$new_plagevac = new CPlageVacances();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",         $user);
$smarty->assign("plages_vac",   $plages_vac);
$smarty->assign("user_id",      $user_id);
$smarty->assign("new_plagevac", $new_plagevac);
$smarty->display("inc_liste_plages_vac.tpl");
?>