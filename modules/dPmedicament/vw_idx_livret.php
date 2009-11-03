<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$lettre = CValue::get("lettre");

$listProduits = array();

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

$tabLettre = range('A', 'Z');

// --- Chargement de l'arbre ATC ---
$codeATC     = CValue::get("codeATC");
$classeATC   = new CBcbClasseATC();
$chapitreATC = $codeATC ? $classeATC->getLibelle($codeATC) : ''; // Nom du chapitre selectionné
$arbreATC    = $classeATC->loadArbre($codeATC); // Chargements des sous chapitres
 
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