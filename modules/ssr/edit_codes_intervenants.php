<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

global $AppUI, $m, $tab;

$current = intval(CValue::get('current', 0));
$step    = intval(CValue::get('step', 20));

$intervenant = new CIntervenantCdARR();
$intervenants = $intervenant->loadList(null, "code");

$mediuser = new CMediusers();

$ljoin = array();
$ljoin["users"] = "users.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

$where = array();
$where["users_mediboard.actif"] = "= '1'";
$where["functions_mediboard.group_id"] = "= '" . CGroups::loadCurrent()->_id . "'";

$limit = "$current, $step";
$order = "users.user_last_name ASC, users.user_first_name ASC";
$total = $mediuser->countList($where, $order, null, null, $ljoin);
$mediusers = $mediuser->loadList($where, $order, $limit, null, $ljoin);

foreach($mediusers as &$_mediuser) {
  $_mediuser->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"    , $mediuser);

$smarty->assign("intervenants", $intervenants);
$smarty->assign("mediusers"   , $mediusers);

$smarty->assign("current"     , $current);
$smarty->assign("step"        , $step);
$smarty->assign("total"       , $total);

$smarty->display("edit_codes_intervenants.tpl");

?>