<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$menu_id  = CValue::getOrSession("menu_id" , null);
$repas_id = CValue::get("repas_id" , null);
if($menu_id == ""){
  $menu_id = null;
}

$menu = new CMenu;
$menu->load($menu_id);

$repas = new CRepas;
$repas->load($repas_id);

// Chargement des plat complémentaires
$plats     = new CPlat;
$listPlats = array();
$where              = array();
$where["typerepas"] = $ds->prepare("= %",$menu->typerepas);
$order              = "nom";
foreach($plats->_specs["type"]->_list as $key => $value){
  $listPlats[$value] = array();
  
  if($menu->modif){
    $where["type"] = $ds->prepare("= %",$value);
    $listPlats[$value] = $plats->loadList($where, $order);
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("menu_id"   , $menu_id);
$smarty->assign("menu"      , $menu);
$smarty->assign("listPlats" , $listPlats);
$smarty->assign("plats"     , $plats);
$smarty->assign("repas"     , $repas);

$smarty->display("inc_repas.tpl");
?>