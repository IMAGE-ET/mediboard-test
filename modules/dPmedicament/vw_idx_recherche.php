<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $AppUI, $can, $m;

// Par default, recherche par nom
$type_recherche = mbGetValueFromGet("type_recherche", "nom");

// Texte recherché (nom, cip, ucd)
$produit  = mbGetValueFromGet("produit");
$dialog = mbGetValueFromGet("dialog");
// Recherche des elements supprimés
$supprime = mbGetValueFromGet("supprime", 0);

// Parametres de recherche
if($type_recherche == "nom") {
  $param_recherche = mbGetValueFromGet("position_text", "debut");
}
if($type_recherche == "cip") {
  $param_recherche = "1";
}
if($type_recherche == "ucd") {
  $param_recherche = "2";
}

$produits = array();

// Recherche du produit
$mbProduit = new CBcbProduit();

$produits = $mbProduit->searchProduit($produit, $supprime, $param_recherche);


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dialog", $dialog);
$smarty->assign("supprime", $supprime);
$smarty->assign("type_recherche", $type_recherche);
$smarty->assign("mbProduit", $mbProduit);
$smarty->assign("produits", $produits);
$smarty->assign("produit", $produit);
$smarty->display("vw_idx_recherche.tpl");

?>