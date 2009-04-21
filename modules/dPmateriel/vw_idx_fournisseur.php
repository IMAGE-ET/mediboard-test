<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$fournisseur_id = mbGetValueFromGetOrSession("fournisseur_id");

// Chargement du fournisseur demandé
$fournisseur=new CFournisseur;
$fournisseur->load($fournisseur_id);
$fournisseur->loadRefsBack();
foreach($fournisseur->_ref_references as $key => $value) {
  $fournisseur->_ref_references[$key]->loadRefsFwd();
}

//Chargement de tous les fournisseur
$lstfournisseur = new CFournisseur;
$where = array();
$listFournisseur = $lstfournisseur->loadList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listFournisseur", $listFournisseur);
$smarty->assign("fournisseur"    , $fournisseur    );

$smarty->display("vw_idx_fournisseur.tpl");
?>
