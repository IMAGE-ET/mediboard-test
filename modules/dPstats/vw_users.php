<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);
$user    = new CMediusers;
$user->load($user_id);
$listUsers = $user->loadListFromType();
$debutlog  = mbGetValueFromGetOrSession("debutlog", mbDate("-1 WEEK"));
$finlog    = mbGetValueFromGetOrSession("finlog", mbDate());

$debutact      = mbGetValueFromGetOrSession("debutact", mbDate());
$finact        = mbGetValueFromGetOrSession("finact", mbDate());

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