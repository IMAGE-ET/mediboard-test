<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if(!CModule::getActive('bcb')){
  CAppUI::stepMessage(UI_MSG_ERROR, "Le module de médicament autonome est en cours de developpement. 
  Pour être utilisé, ce module a pour le moment besoin d'être connecté à une base de données de médicaments externe");
  return;
}

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