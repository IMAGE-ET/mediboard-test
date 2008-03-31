<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

// Recuperation du code_cip
$code_cip = mbGetValueFromGet("code_cip");
$line_id  = mbGetValueFromGet("line_id");
$inLivret = mbGetValueFromGet("inLivret");

// Chargement du produit
$produit = new CBcbProduit();
$produit->load($code_cip);

// Chargement des equivalents
if($inLivret){
	$produit->loadRefsEquivalentsInLivret();
} else {
  $produit->loadRefsEquivalents();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("line_id", $line_id);
$smarty->assign("equivalents", $produit->_ref_equivalents);

$smarty->display("vw_equivalents.tpl");


?>