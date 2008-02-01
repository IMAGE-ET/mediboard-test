<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $g;

$produit_livret_id = mbGetValueFromGet("produit_id");

// Chargement du produit
$produit_livret = new CProduitLivretTherapeutique();
$produit_livret->produit_livret_id = $produit_livret_id;
$produit_livret->loadMatchingObject();
$produit_livret->loadRefProduit();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("produit_livret", $produit_livret);

$smarty->display("edit_produit_livret.tpl");

?>