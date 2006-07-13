<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

require_once($AppUI->getModuleClass("dPmateriel", "refmateriel"));
require_once($AppUI->getModuleClass("dPmateriel", "category"   ));
require_once($AppUI->getModuleClass("dPmateriel", "fournisseur"));

$reference_id = mbGetValueFromGetOrSession("reference_id");

// Chargement de la reference demandé
$reference=new CRefMateriel;
$reference->load($reference_id);
if($reference_id = mbGetValueFromGet("fournisseur_id")){
  $reference->fournisseur_id = $reference_id;
}

//Chargement de toutes les réferences
$lstreference = new CRefMateriel;
$where = array();
$listReference = $lstreference->loadList($where);
foreach($listReference as $key => $value) {
  $listReference[$key]->loadRefsFwd();
}

//Liste des categories Pour liste des materiels
$Cat = new CCategory;
$listCategory = $Cat->loadList();
foreach($listCategory as $key => $value) {
  $listCategory[$key]->loadRefsBack();
}

// Liste des Fournisseur
$Fournisseur = new CFournisseur;
$listFournisseur = $Fournisseur->loadList();

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listCategory"   ,$listCategory   );
$smarty->assign("listFournisseur",$listFournisseur);
$smarty->assign("listReference"  , $listReference );
$smarty->assign("reference"      , $reference     );

$smarty->display("vw_idx_refmateriel.tpl");
?>
