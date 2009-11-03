<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$code_cip = CValue::get("code_cip");
$line_id  = CValue::get("line_id");
$inLivret = CValue::get("inLivret");

// Initialisations
$classe_ATC = new CBcbClasseATC();  
$classe_BCB = new CBcbClasseTherapeutique();
$equivalents_strictes_BCB = array();
$equivalents_strictes_ATC = array();
$equivalents_thera_ATC = array();
$code_thera_ATC = "";
$libelle_thera_ATC = "";

// Chargement du produit
$produit = new CBcbProduit();
$produit->load($code_cip);


// Equivalents strictes BCB
if($inLivret){
  $produit->loadRefsEquivalentsInLivret();
} else {
  $produit->loadRefsEquivalents();  
}
$equivalents_strictes_BCB = $produit->_ref_equivalents;
$arbre_BCB = $classe_BCB->searchTheraProduit($produit->code_cip);
$code_stricte_BCB = $arbre_BCB["0"]->Code;
$libelle_stricte_BCB = $classe_BCB->getLibelle($code_stricte_BCB);

// Equivalents strictes ATC
$arbre_ATC = $classe_ATC->searchATCProduit($produit->code_cip);
$code_stricte_ATC = $arbre_ATC["0"]->Code;
$libelle_stricte_ATC = $classe_ATC->getLibelle($code_stricte_ATC);
if($inLivret){ 
  $equivalents_strictes_ATC = $classe_ATC->loadRefProduitsLivret($code_stricte_ATC);
} else {
  $classe_ATC->loadRefsProduits($code_stricte_ATC);
  foreach($classe_ATC->_ref_produits as $key => $_produit){
    $mbproduit = new CBcbProduit();
    $mbproduit->load($_produit->CodeCIP);
    $_produit->_ref_produit = $mbproduit;
    $equivalents_strictes_ATC[] = $_produit;
  }
}

// Equivalents therapeutiques ATC
if($inLivret){
  $produit->loadClasseATC();
	$equivalents_thera_ATC = $classe_ATC->loadRefProduitsLivret($produit->_ref_ATC_2_code);
	$libelle_thera_ATC = $classe_ATC->getLibelle($produit->_ref_ATC_2_code);
  $code_thera_ATC = $produit->_ref_ATC_2_code;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line_id", $line_id);
$smarty->assign("equivalents_strictes_ATC", $equivalents_strictes_ATC);
$smarty->assign("equivalents_strictes_BCB", $equivalents_strictes_BCB);
$smarty->assign("equivalents_thera_ATC", $equivalents_thera_ATC);
$smarty->assign("code_stricte_ATC", $code_stricte_ATC);
$smarty->assign("libelle_stricte_ATC", $libelle_stricte_ATC);
$smarty->assign("code_stricte_BCB", $code_stricte_BCB);
$smarty->assign("libelle_stricte_BCB", $libelle_stricte_BCB);
$smarty->assign("code_thera_ATC", $code_thera_ATC);
$smarty->assign("libelle_thera_ATC", $libelle_thera_ATC);
$smarty->assign("inLivret", $inLivret);
$smarty->assign("produit", $produit);
$smarty->display("vw_equivalents.tpl");

?>