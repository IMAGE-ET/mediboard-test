<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $g;

$lettre = mbGetValueFromGet("lettre");

$listProduits = array();

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

$tabLettre = range('A', 'Z');

// --- Chargement de l'arbre ATC ---
$codeATC = mbGetValueFromGet("codeATC");
$classeATC = new CBcbClasseATC();
// Nom du chapitre selectionne
$chapitreATC = $classeATC->getLibelle($codeATC);
// Chargements des sous chapitres
$arbreATC = $classeATC->loadArbre($codeATC);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listProduits", $listProduits);
$smarty->assign("arbreATC", $arbreATC);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("chapitreATC", $chapitreATC);
$smarty->assign("lettre", $lettre);
$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);
$smarty->assign("tabLettre", $tabLettre);

$smarty->display("vw_idx_livret.tpl");

?>