<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $AppUI, $can, $m;

$produit  = mbGetValueFromPost("produit");
$supprime = mbGetValueFromPost("supprime", 0);
$position_text = mbGetValueFromPost("position_text", "debut");

// Recherche par nom de produit
$mbProduit = new CBcbProduit();
$mbProduit->searchProduit($produit, $supprime, $position_text);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("supprime", $supprime);
$smarty->assign("position_text", $position_text);
$smarty->assign("mbProduit", $mbProduit);
$smarty->assign("produit", $produit);
$smarty->display("vw_idx_recherche.tpl");

?>