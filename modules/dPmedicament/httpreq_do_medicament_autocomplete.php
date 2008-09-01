<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $dPconfig, $g;// Recherche du produit

$produit = mbGetValueFromPost("produit", "aaa");
$inLivret = mbGetValueFromPost("inLivret", 0);
$produit_max = mbGetValueFromGet("produit_max", 10);

$mbProduit = new CBcbProduit();

// Recherche dans la bcb
$produits = $mbProduit->searchProduitAutocomplete($produit, $produit_max, $inLivret);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("produits", $produits);
$smarty->assign("nodebug", "true");
$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>