<?php 

/**
 * Liste des users principaux et secondaires d'une fonction 
 *  
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$function_id    = CValue::getOrSession("function_id");
$page_function  = intval(CValue::get('page_function', 0));

$step_sec_function = 10;
$primary_users = array();
$total_sec_functions = null;

$function = new CFunctions();
$function->load($function_id);

$total_sec_functions = $function->countBackRefs("users");
/** @var CMediusers[] $primary_users */
$primary_users = $function->loadBackRefs("users", null, "$page_function, $step_sec_function");
CStoredObject::massLoadFwdRef($primary_users, "_profile_id");
foreach ($primary_users as $_mediuser) {
  $_mediuser->loadRefProfile();
}

/** @var CSecondaryFunction[] $secondaries_functions */
$secondaries_functions = $function->loadBackRefs("secondary_functions");
$users = CMbObject::massLoadFwdRef($secondaries_functions, "user_id");
CStoredObject::massLoadFwdRef($users, "_profile_id");
foreach ($secondaries_functions as $_sec_function) {
  $_sec_function->loadRefUser();
  $_sec_function->_ref_user->loadRefProfile();
}

$smarty = new CSmartyDP();

$smarty->assign("function"           , $function);
$smarty->assign("primary_users"      , $primary_users);
$smarty->assign("total_sec_functions", $total_sec_functions);
$smarty->assign("page_function"      , $page_function);
$smarty->assign("utypes"             , CUser::$types);
$smarty->assign("secondary_function" , new CSecondaryFunction());

$smarty->display("inc_prim_secon_users.tpl");