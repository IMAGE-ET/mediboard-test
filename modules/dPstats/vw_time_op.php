<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

if(function_exists("date_default_timezone_set")) {
  date_default_timezone_set("UTC");
}

global $AppUI, $canRead, $canEdit, $m;
require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$codeCCAM   = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));
$prat_id    = mbGetValueFromGetOrSession("prat_id", 0);
$intervalle = mbGetValueFromGetOrSession("intervalle", 2);

$typeVue    = mbGetValueFromGetOrSession("typeVue", 0);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

if($typeVue){
  // Stat des temps de préparation
  include("inc_vw_timeop_prepa.php");
}else{
  // Stat des temps opératoires
  include("inc_vw_timeop_op.php");
}


// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

if($typeVue){
  $smarty->assign("result", $result); 
}else{
  $smarty->assign("prat_id"   , $prat_id   );
  $smarty->assign("codeCCAM"  , $codeCCAM  );
  $smarty->assign("listPrats" , $listPrats );
  $smarty->assign("listOps"   , $listOps   );
}

$smarty->assign("intervalle", $intervalle);
$smarty->assign("total"     , $total     );  

$smarty->assign("typeVue", $typeVue);

$smarty->display("vw_time_op.tpl");

?>