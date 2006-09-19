<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $canRead, $canEdit, $m;


if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$ei_categorie_id = mbGetValueFromGetOrSession("ei_categorie_id",0);
$ei_item_id = mbGetValueFromGetOrSession("ei_item_id",0);

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
$listItems = $listItems->loadList(null,"nom");
foreach($listItems as $key => $value) {
  $listItems[$key]->loadRefsFwd();
}

require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("categorie"      , $categorie);
$smarty->assign("item"           , $item);
$smarty->assign("listCategories" , $listCategories);
$smarty->assign("listItems"      , $listItems);

$smarty->display("vw_edit_ei.tpl"); 
?>