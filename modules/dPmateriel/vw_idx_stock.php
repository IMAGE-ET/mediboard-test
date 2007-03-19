<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $can, $m;

$can->needsRead();

getChildClasses();

$stock_id = mbGetValueFromGetOrSession("stock_id");

// Récupération des données pour le stock choisi 
$stock = new CStock;
$stock->load($stock_id);
if($stock_id = mbGetValueFromGet("materiel_id")){
  $stock->materiel_id = $stock_id;
}

// Récupération de la liste des Stock
$liststock = new CStock;
$listStock = $liststock->loadList();
foreach($listStock as $key => $value) {
  $listStock[$key]->loadRefsFwd();
  $listStock[$key]->_ref_materiel->loadRefsFwd();
}

// Liste des Groupes
$Groupes = new CGroups;
$listGroupes = $Groupes->loadList();

//Liste des categories
$Cat = new CCategory;
$listCategory = $Cat->loadList();
foreach($listCategory as $key => $value) {
  $listCategory[$key]->loadRefsBack();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("stock"       , $stock       );
$smarty->assign("listGroupes" , $listGroupes );
$smarty->assign("listCategory", $listCategory);
$smarty->assign("listStock"   , $listStock   );

$smarty->display("vw_idx_stock.tpl");

?>