<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$user      = CMediusers::get(CValue::getOrSession("user_id"));
$listUsers = $user->loadListFromType();
$debutlog  = CValue::getOrSession("debutlog", mbDate("-1 WEEK"));
$finlog    = CValue::getOrSession("finlog", mbDate());

$debutact      = CValue::getOrSession("debutact", mbDate());
$finact        = CValue::getOrSession("finact", mbDate());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_id"  , $user_id  );
$smarty->assign("listUsers", $listUsers);
$smarty->assign("debutlog" , $debutlog );
$smarty->assign("finlog"   , $finlog   );

$smarty->assign("debutact", $debutact);
$smarty->assign("finact", $finact);

$smarty->display("vw_users.tpl");

?>