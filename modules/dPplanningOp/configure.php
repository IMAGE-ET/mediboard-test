<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $canAdmin, $canRead, $canEdit, $m, $tab;

if(!$canAdmin) {
    $AppUI->redirect("m=system&a=access_denied");
}
/*
if(!isset($dPconfig["dPplanningOp"]["operation"])){
  $dPconfig["dPplanningOp"]["operation"] = array (
    "duree_deb"        => "0",
    "duree_fin"        => "10",
    "hour_urgence_deb" => "0",
    "hour_urgence_fin" => "23",
    "min_intervalle"   => "15"
  );
}
if(!isset($dPconfig["dPplanningOp"]["sejour"])){  
  $dPconfig["dPplanningOp"]["sejour"] = array (
    "heure_deb"      => "0",
    "heure_fin"      => "23",
    "min_intervalle" => "15"
  );
}
*/
$listHours = array();
for ($i = 0; $i <=23; $i++) {
  if($i<=9){
    $listHours[] = "0".$i;
  }else{
    $listHours[] = $i;
  }
}

$listInterval = array("5","10","15","20","30");

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("listHours"    , $listHours);
$smarty->assign("listInterval" , $listInterval);
$smarty->assign("configOper"   , $dPconfig["dPplanningOp"]["operation"]);
$smarty->assign("configSejour" , $dPconfig["dPplanningOp"]["sejour"]);

$smarty->display("configure.tpl");
?>