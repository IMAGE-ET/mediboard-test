<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$codeBCB = CValue::get("codeBCB");
$dialog = CValue::get("dialog");

$classeBCB = new CBcbClasseTherapeutique();

// Nom du chapitre selectionne
$chapitreBCB = $classeBCB->getLibelle($codeBCB);

// Chargements des sous chapitres
$arbreBCB = $classeBCB->loadArbre($codeBCB);

// Chargement des produits
$classeBCB->loadRefsProduits($codeBCB);

// Calcul du niveau du code
$niveauCodeBCB = $classeBCB->getNiveau($codeBCB);

// Calcul du code de niveau superieur
$codeNiveauSup = $classeBCB->getCodeNiveauSup($codeBCB);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("niveauCodeBCB", $niveauCodeBCB);
$smarty->assign("codeNiveauSup", $codeNiveauSup);
$smarty->assign("chapitreBCB", $chapitreBCB);
$smarty->assign("codeBCB", $codeBCB);
$smarty->assign("dialog", $dialog);
$smarty->assign("arbreBCB", $arbreBCB);
$smarty->assign("classeBCB", $classeBCB);

$smarty->display("inc_vw_arbre_BCB.tpl");


?>