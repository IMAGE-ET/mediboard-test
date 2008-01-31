<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $AppUI, $can, $m;

// Onglet ouvert par défaut
$modes_recherche = array("produit"   => "one",
                         "classe"    => "two",
                         "composant" => "three",
                         "DC_search" => "four");
$onglet_recherche = $modes_recherche[mbGetValueFromGet("onglet_recherche", "produit")];


$produit   = mbGetValueFromGet("produit");
$composant = mbGetValueFromGet("composant");
$composition = new CBcbComposition();


if($composant){
  $composition->searchComposant($composant);
}

$DCI = new CBcbDCI();
$DC_search = mbGetValueFromGet("DC_search");
$tabDCI = array();

if($DC_search){
  $tabDCI = $DCI->searchDCI($DC_search);
}


// --- RECHERCHE PRODUITS ---
// Par default, recherche par nom
$type_recherche = mbGetValueFromGet("type_recherche", "nom");
// Texte recherché (nom, cip, ucd)
$dialog = mbGetValueFromGet("dialog");
// Recherche des elements supprimés
$supprime = mbGetValueFromGet("supprime", 0);
// Parametres de recherche
if($type_recherche == "nom") {
  $param_recherche = mbGetValueFromGet("position_text", "debut");
}
if($type_recherche == "cip") {
  $param_recherche = "1";
}
if($type_recherche == "ucd") {
  $param_recherche = "2";
}
$produits = array();
// Recherche du produit
$mbProduit = new CBcbProduit();
$produits = $mbProduit->searchProduit($produit, $supprime, $param_recherche);

// --- RECHERCHE PAR CLASSES ---
$classeATC = new CBcbClasseATC();
$classeATC->loadRefsProduits();
$arbreATC = $classeATC->loadArbre();
$niveauCodeATC = "";

$classeBCB = new CBcbClasseTherapeutique();
$classeBCB->loadRefsProduits();
$arbreBCB = $classeBCB->loadArbre();
$niveauCodeBCB = "";


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("onglet_recherche", $onglet_recherche);

$smarty->assign("composition", $composition);
$smarty->assign("tabDCI", $tabDCI);
$smarty->assign("DC_search", $DC_search);
$smarty->assign("DCI_code", "");
$smarty->assign("tabViewProduit", "");

$smarty->assign("composant", $composant);
$smarty->assign("code", "");

$smarty->assign("niveauCodeATC", $niveauCodeATC);
$smarty->assign("niveauCodeBCB", $niveauCodeBCB);
$smarty->assign("arbreATC", $arbreATC);
$smarty->assign("arbreBCB", $arbreBCB);
$smarty->assign("classeATC", $classeATC);
$smarty->assign("classeBCB", $classeBCB);
$smarty->assign("chapitreATC", "");
$smarty->assign("chapitreBCB", "");
$smarty->assign("codeATC", "");
$smarty->assign("codeBCB", "");

$smarty->assign("dialog", $dialog);
$smarty->assign("supprime", $supprime);
$smarty->assign("type_recherche", $type_recherche);
$smarty->assign("mbProduit", $mbProduit);
$smarty->assign("produits", $produits);
$smarty->assign("produit", $produit);

$smarty->display("vw_idx_recherche.tpl");

?>