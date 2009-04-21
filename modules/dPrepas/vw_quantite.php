<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsEdit();
$ds = CSQLDataSource::get("std");
$service_id = mbGetValueFromGetOrSession("service_id" , null);
$type       = mbGetValueFromGetOrSession("type"       , null);
$date       = mbGetValueFromGetOrSession("date"       , mbDate());

$listRepas   = new CRepas;
$where = array();
$where["group_id"]    = $ds->prepare("= %", $g);
$where["date"]        = $ds->prepare("= %", $date);
$where["typerepas"] = $ds->prepare("= %", $type);

$ljoin = array("menu" => "repas.menu_id = menu.menu_id");
$listRepas = $listRepas->loadList($where, null, null, null, $ljoin);

$plats = new CPlat;
$typesPlats = $plats->_specs["type"]->_list;

$listMenu = array();
$listPlat = array();
$listRemplacement = array();
foreach($typesPlats as $typePlat){
  $listPlat[$typePlat]         = array();
}

foreach($listRepas as $keyRepas=>&$repas){
  $repas->loadRefMenu();
  $menu =& $listMenu[$repas->menu_id];
  if(!isset($listMenu[$repas->menu_id])){
    $menu["obj"]   =& $repas->_ref_menu;
    $menu["total"] = 1;
    foreach($typesPlats as $typePlat){
      $menu["detail"][$typePlat] = 0;
    }
  }else{
    $menu["total"]++;
  }
  
  foreach($typesPlats as $typePlat){
    $plat_id =& $repas->$typePlat;
    if($plat_id){
      $plat_remplacement =& $listRemplacement[$typePlat][$plat_id];
      if(isset($plat_remplacement)){
        $plat_remplacement["nb"]++;
      }else{
        $plats2 = new CPlat;
        $plats2->load($plat_id);
        $plat_remplacement = array("obj" => $plats2 , "nb" => 1);
      }
    }else{
      $menu["detail"][$typePlat]++;
    }
  }
  
} 
// Liste des types de repas
$listTypeRepas = new CTypeRepas;
$order = "debut, fin, nom";
$listTypeRepas = $listTypeRepas->loadList(null,$order);

// Liste des services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);
foreach($services as &$service){
  $service->validationRepas($date);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listMenu"         , $listMenu);
$smarty->assign("listRemplacement" , $listRemplacement);
$smarty->assign("date"             , $date);
$smarty->assign("plats"            , $plats);
$smarty->assign("listTypeRepas"    , $listTypeRepas);
$smarty->assign("type"             , $type);
$smarty->assign("services"         , $services);
$smarty->assign("service_id"       , $service_id);

$smarty->display("vw_quantite.tpl");
?>