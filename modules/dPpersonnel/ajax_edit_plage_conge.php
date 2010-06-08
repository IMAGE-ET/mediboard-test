<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user_id  = CValue::postOrSession("user_id");
$plage_id = CValue::post("plage_id");
$user     = new CMediusers();
$user->load($user_id);

// Chargement de la plage
$plageconge = new CPlageConge();
$plageconge->user_id = $user_id;
$plageconge->load($plage_id);

// Remplaçants disponibles
$replacers = $user->loadUsers();
unset($replacers[$user->_id]);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",      $user);
$smarty->assign("plageconge",  $plageconge);
$smarty->assign("replacers", $replacers);
$smarty->display("inc_edit_plage_conge.tpl");
?>