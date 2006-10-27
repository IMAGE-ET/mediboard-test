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

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$typeVue = mbGetValueFromGetOrSession("typeVue", 2);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

if($typeVue == 0){
  // Stat des temps de préparation
  include("inc_vw_timeop_op.php");
} elseif($typeVue == 1) {
  // Stat des temps opératoires
  include("inc_vw_timeop_prepa.php");
} else {
  // Stat des temps d'hospitalisation
  include("inc_vw_timehospi.php");
}


// Création du template
$smarty = new CSmartyDP(1);

if($typeVue == 0 || $typeVue == 2) {
  $smarty->assign("prat_id"  , $prat_id  );
  $smarty->assign("codeCCAM" , $codeCCAM );
  $smarty->assign("listPrats", $listPrats);
}

if($typeVue == 2) {
  $smarty->assign("type", $type);
}

$smarty->assign("listTemps", $listTemps);
$smarty->assign("total", $total);
$smarty->assign("typeVue", $typeVue);

$smarty->display("vw_time_op.tpl");

?>