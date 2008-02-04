<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $g;

$produit_livret_id = mbGetValueFromGet("produit_id");

$lettre = mbGetValueFromGet("lettre");
$codeATC = mbGetValueFromGet("codeATC");
$code_cip = mbGetValueFromGet("code_cip");

// Chargement du produit
$produit_livret = new CProduitLivretTherapeutique();
$produit_livret->produit_livret_id = $produit_livret_id;
$produit_livret->loadMatchingObject();
$produit_livret->loadRefProduit();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("code_cip", $code_cip);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("lettre", $lettre);
$smarty->assign("produit_livret", $produit_livret);

$smarty->display("edit_produit_livret.tpl");

?>