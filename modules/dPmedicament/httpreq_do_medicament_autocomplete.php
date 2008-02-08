<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $dPconfig, $g;// Recherche du produit

$produit = mbGetValueFromPost("produit", "aaa");

$produit_max = mbGetValueFromPost("produit_max", 10);

$mbProduit = new CBcbProduit();

// Recherche dans la bcb
$produits = $mbProduit->searchProduit($produit, 1, "debut", 1, $produit_max);
    
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("produits", $produits);

$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>