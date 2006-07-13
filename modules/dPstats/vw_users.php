<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);
$user    = new CMediusers;
$user->load($user_id);
$listUsers = $user->loadListFromType();
$debutlog  = mbGetValueFromGetOrSession("debutlog", mbDate("-1 WEEK"));
$finlog    = mbGetValueFromGetOrSession("finlog", mbDate());

// Création du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("user_id"  , $user_id  );
$smarty->assign("listUsers", $listUsers);
$smarty->assign("debutlog" , $debutlog );
$smarty->assign("finlog"   , $finlog   );

$smarty->display("vw_users.tpl");

?>