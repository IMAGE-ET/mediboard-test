<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $can, $m;

$can->needsRead();

$employecab_id = mbGetValueFromGetOrSession("employecab_id", null);

$user = new CMediusers;
$user->load($AppUI->user_id);

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