<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$user_id  = CValue::getOrSession("user_id");
$plage_id = CValue::getOrSession("plage_id");
$user     = new CMediusers();
$user->load($user_id);


$new_plagevac = new CPlageVacances();
$new_plagevac->user_id = $user_id;

$plagevac = new CPlageVacances();
$plagevac->load($plage_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",         $user);
$smarty->assign("user_id",      $user_id);
$smarty->assign("plage_id",     $plage_id);
$smarty->assign("plagevac",     $plagevac);
$smarty->assign("new_plagevac", $new_plagevac);
$smarty->display("vw_idx_edit_plagevacances.tpl");
?>