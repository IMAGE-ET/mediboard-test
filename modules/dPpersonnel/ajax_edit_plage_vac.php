<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user_id  = CValue::getOrSession("user_id");
$plage_id = CValue::get("plage_id");
$user     = new CMediusers();
$user->load($user_id);

// Chargement de la plage
$plagevac = new CPlageVacances();
$plagevac->user_id = $user_id;
$plagevac->load($plage_id);

// Remplaçants disponibles
$replacers = $user->loadUsers();
unset($replacers[$user->_id]);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",      $user);
$smarty->assign("plagevac",  $plagevac);
$smarty->assign("replacers", $replacers);
$smarty->display("inc_edit_plage_vac.tpl");
?>