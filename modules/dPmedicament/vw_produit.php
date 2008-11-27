<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

// Recuperation du CIP du produit
$cip = mbGetValueFromGetOrSession("CIP");

// Chargement du produit
//$mbProduit = new CBcbProduit();
//$mbProduit->load($cip, true);

$mbProduit = CBcbProduit::get($cip, true);

// Chargement des donnees technico-reglementaires
$mbProduit->loadRefEconomique();

// Chargement des classes ATC du produit
$mbProduit->loadClasseATC();

// Chargement des classes therapeutiques du produit
$mbProduit->loadClasseTherapeutique();

// Creation du template
$smarty = new CSmartyDP();
$smarty->assign("mbProduit", $mbProduit);
$smarty->display("vw_produit.tpl");

?>