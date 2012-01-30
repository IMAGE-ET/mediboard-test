<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$forceChange = CValue::request("forceChange");

$user = new CUser;
$user->load(CAppUI::$user->_id);
$user->updateSpecs();
$user->isLDAPLinked();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user"       , $user);
$smarty->assign("forceChange", $forceChange);
$smarty->display("change_password.tpl");
