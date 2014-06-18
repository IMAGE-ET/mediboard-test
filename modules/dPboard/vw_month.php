<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user = CMediusers::get();
$user->isSecretaire();
$user->isPraticien();

$date = CValue::get('date', CMbDT::date());
$prat_id = CValue::get('praticien_id');
$function_id = CValue::get("function_id");


$prat = new CMediusers();
$prat->load($prat_id);

$function = new CFunctions();
$listFunc = CMediusers::loadFonctions(PERM_EDIT);

/** @var CMediusers[] $listPrat */
$listPrat = $prat->loadListWithPerms(PERM_EDIT, null);

foreach ($listPrat as $_prat_id => $_prat) {
  if (!$_prat->isPraticien()) {
    unset($listPrat[$_prat_id]);
    continue;
  }
}

usort(
  $listPrat, function ($a, $b) {
    return strcmp($a->_user_last_name, $b->_user_last_name);
  }
);


$calendar = new CPlanningMonth($date);

// smarty
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("prev", $date);
$smarty->assign("next", $date);
$smarty->assign("prat", $prat);
$smarty->assign("listPrat", $listPrat);

$smarty->assign("user", $user);

$smarty->assign("listFunc", $listFunc);
$smarty->assign("function_id", $function_id);

$smarty->display("vw_month.tpl");