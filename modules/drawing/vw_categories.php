<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// users
$user = new CMediusers();
$users = $user->loadUsers(PERM_EDIT);

// functions
$function = new CFunctions();
$functions = $function->loadListWithPerms(PERM_EDIT);

// smarty
$smarty = new CSmartyDP();
$smarty->assign("users", $users);
$smarty->assign("functions", $functions);
$smarty->display("vw_categories.tpl");