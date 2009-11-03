<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $m;

$group_id = CValue::get("group_id", null);

$listCab       = array();
$salles        = array();
$services      = array();
$etablissement = new CGroups;

if($group_id && $etablissement->load($group_id)){
  $where = array();
  $where["group_id"] = "= '$group_id'";
  
  
  //Salles
  $order = "nom";
  $salles = new CSalle;
  $salles = $salles->loadList($where, $order);
  
  // Cabinet
  $where["type"] = " ='cabinet'";
  $order = "text";
  $listCab = new CFunctions;
  $listCab = $listCab->loadList($where, $order);

  // Services
  $services = new CService;
  $where = array();
  $where["group_id"] = "= '$group_id'";
  $order = "nom";
  $services = $services->loadList($where, $order);
  
  if(count($listCab)){
    $list_5 = CMbArray::createRange(0,5, true);
  }
}else{
  $group_id = null;
  $list_5 = CMbArray::createRange(1,5, true); 
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign("salles"       , $salles);
$smarty->assign("listCab"      , $listCab);
$smarty->assign("services"     , $services);
$smarty->assign("list_5"       , $list_5);
$smarty->assign("group_id"     , $group_id);

$smarty->display("inc_echantillonnage_etape2.tpl");
?>