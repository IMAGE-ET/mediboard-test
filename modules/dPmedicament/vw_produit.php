<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Recuperation du CIP du produit
$code_cip = CValue::get("code_cip");
$code_ucd = CValue::get("code_ucd");
$code_cis = CValue::get("code_cis");

// Chargement de tous les produits correspondants au CIS ou a l'UCD
$produits = array();
$_produits = array();

if($code_cis){
  $_produits = CBcbProduit::getProduitsFromCIS($code_cis);
} elseif ($code_ucd){
  $_produits = CBcbProduit::getProduitsFromUCD($code_ucd);
}

foreach($_produits as $_produit){
  $curr_cip = $_produit["CODE_CIP"];
  $produit = new CBcbProduit();
  $produits[$curr_cip] = CBcbProduit::get($curr_cip, false);
}

if(count($produits) && !$code_cip){
  $code_cip = reset($produits)->code_cip;  
}

// Chargement du produit et de la monographie
$mbProduit = new CBcbProduit();
if($code_cip){
	$mbProduit = CBcbProduit::get($code_cip, true);
	$mbProduit->loadRefMonographie();
	$mbProduit->getStatut();
	$mbProduit->getAgrement();
	$mbProduit->getSuppression();
	$mbProduit->loadRefComposition();
	if($mbProduit->code_cip){
		// Chargement des donnees technico-reglementaires
		$mbProduit->loadRefEconomique();
		// Chargement des classes ATC du produit
		$mbProduit->loadClasseATC();
		// Chargement des classes therapeutiques du produit
		$mbProduit->loadClasseTherapeutique();
	}
}

// Creation du template
$smarty = new CSmartyDP();
$smarty->assign("mbProduit", $mbProduit);
$smarty->assign("produits", $produits);
$smarty->assign("code_ucd", $code_ucd);
$smarty->assign("code_cis", $code_cis);
$smarty->assign("code_cip", $code_cip);
$smarty->display("vw_produit.tpl");

?>