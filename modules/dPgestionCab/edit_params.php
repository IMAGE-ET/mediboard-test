<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

CCanDo::checkRead();

$employecab_id = CValue::getOrSession("employecab_id", null);

$user = CMediusers::get();

$employe = new CEmployeCab;
$where = array();
$where["function_id"] = "= '$user->function_id'";

$listEmployes = $employe->loadList($where);
if($employecab_id) {
  $employe =& $listEmployes[$employecab_id];
} else {
  $employe->function_id = $user->function_id;
}

$paramsPaie = new CParamsPaie();
if($employe->employecab_id) {
  $paramsPaie->loadFromUser($employe->employecab_id);
  $paramsPaie->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("employe"      , $employe);
$smarty->assign("paramsPaie"   , $paramsPaie);
$smarty->assign("listEmployes" , $listEmployes);

$smarty->display("edit_params.tpl");
?>