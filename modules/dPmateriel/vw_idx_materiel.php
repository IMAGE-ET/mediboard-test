<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$materiel_id = CValue::getOrSession("materiel_id", null);

// Chargement du matériel demandé
$materiel=new CMateriel;
$materiel->load($materiel_id);
$materiel->loadRefsBack();

foreach($materiel->_ref_stock as $key => $value) {
  $materiel->_ref_stock[$key]->loadRefsFwd();
  $materiel->_ref_stock[$key]->_ref_group->loadRefsFwd();
}
foreach($materiel->_ref_refMateriel as $key => $value) {
  $materiel->_ref_refMateriel[$key]->loadRefsFwd();
}


// Liste des Catégories
$Categories = new CCategory;
$listCategories = $Categories->loadList();

//Chargement de tous les matériels
$lstmateriel = new CMateriel;
$where = array();
$listMateriel = $lstmateriel->loadList($where);
foreach($listMateriel as $key => $value) {
  $listMateriel[$key]->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listMateriel"  , $listMateriel  );
$smarty->assign("materiel"      , $materiel      );
$smarty->assign("listCategories", $listCategories);

$smarty->display("vw_idx_materiel.tpl");

?>