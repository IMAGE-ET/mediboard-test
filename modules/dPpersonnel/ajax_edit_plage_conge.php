<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();

$plage_id = CValue::get("plage_id");
$user_id  = CValue::getOrSession("user_id");

$user = CMediusers::get($user_id);

// Chargement de la plage
$plageconge = new CPlageConge();
$plageconge->user_id = $user_id;
$plageconge->load($plage_id);
$plageconge->loadRefsNotes();

// Remplaçants disponibles
$replacers = $user->loadUsers();
unset($replacers[$user->_id]);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",       $user);
$smarty->assign("plageconge", $plageconge);
$smarty->assign("replacers",  $replacers);
$smarty->display("inc_edit_plage_conge.tpl");
