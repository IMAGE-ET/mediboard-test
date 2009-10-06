<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI, $can, $m;

$can->needsAdmin();

$ei_categorie_id = mbGetValueFromGetOrSession("ei_categorie_id",0);
$ei_item_id      = mbGetValueFromGetOrSession("ei_item_id",0);
$vue_item        = mbGetValueFromGetOrSession("vue_item",0);


// Catégorie demandée
$categorie = new CEiCategorie;
if(!$categorie->load($ei_categorie_id)){
  // Cette catégorie n'est pas valide
  $ei_categorie_id = null;
  mbSetValueToSession("ei_categorie_id");
  $categorie = new CEiCategorie;
}else{
  $categorie->loadRefsBack();
}

// Item demandé
$item = new CEiItem;
if(!$item->load($ei_item_id)){
  // Cet item n'est pas valide
  $ei_item_id = null;
  mbSetValueToSession("ei_item_id");
  $item = new CEiItem;
}else{
  $item->loadRefsFwd();
}

// Liste des Catégories
$listCategories = new CEiCategorie;
$listCategories = $listCategories->loadList(null,"nom");

// Liste des Items
$listItems = new CEiItem;
$where = null;
if($vue_item){
  $where = "ei_categorie_id = '$vue_item'";
}
$listItems = $listItems->loadList($where,"ei_categorie_id, nom");
foreach($listItems as $key => $value) {
  $listItems[$key]->loadRefsFwd();
}

$smarty = new CSmartyDP();

$smarty->assign("categorie"      , $categorie);
$smarty->assign("item"           , $item);
$smarty->assign("listCategories" , $listCategories);
$smarty->assign("listItems"      , $listItems);
$smarty->assign("vue_item"       , $vue_item);

$smarty->display("vw_edit_ei.tpl"); 
?>