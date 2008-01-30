<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;// Recherche du produit

$produit = mbGetValueFromPost("produit", "aaa");

$mbProduit = new CBcbProduit();

$produits = $mbProduit->searchProduit($produit, 0, "debut");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("produits", $produits);

$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>