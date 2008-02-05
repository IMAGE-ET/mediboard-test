<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $AppUI, $can, $m, $g;

$lettre = mbGetValueFromGet("lettre");

$listProduits = array();
// Chargement de l'etablissement courant
$etablissement = new CGroups();
$etablissement->load($g);

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

// Chargement du produit
foreach($etablissement->_ref_produits_livret as $key => $produit_livret){
  $produit_livret->loadRefProduit();
}


$tabLettre = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Z");


// --- Chargement de l'arbre ATC ---
$codeATC = mbGetValueFromGet("codeATC");
$classeATC = new CBcbClasseATC();
// Nom du chapitre selectionne
$chapitreATC = $classeATC->getLibelle($codeATC);
// Chargements des sous chapitres
$arbreATC = $classeATC->loadArbre($codeATC);
// Chargement des produits par classes ATC
$classeATC->loadRefsProduits($codeATC);
// Calcul du niveau du code
$niveauCodeATC = $classeATC->getNiveau($codeATC);
// Calcul du code de niveau superieur
$codeNiveauSup = $classeATC->getCodeNiveauSup($codeATC);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listProduits", $listProduits);

$smarty->assign("arbreATC", $arbreATC);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("classeATC", $classeATC);
$smarty->assign("chapitreATC", $chapitreATC);
$smarty->assign("niveauCodeATC", $niveauCodeATC);
$smarty->assign("codeNiveauSup", $codeNiveauSup);

$smarty->assign("lettre", $lettre);
$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);
$smarty->assign("tabLettre", $tabLettre);
$smarty->display("vw_idx_livret.tpl");

?>