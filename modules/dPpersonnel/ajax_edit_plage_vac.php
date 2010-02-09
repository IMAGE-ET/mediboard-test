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
$plage_id = CValue::get("plage_id");
$user     = new CMediusers();
$user->load($user_id);


$plagevac = new CPlageVacances();
$plagevac->user_id = $user_id;
$plagevac->load($plage_id);
if (sizeof($plagevac)==0)
  $plagevac->plage_id = '';

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",         $user);
$smarty->assign("plagevac",     $plagevac);
$smarty->display("inc_edit_plage_vac.tpl");
?>