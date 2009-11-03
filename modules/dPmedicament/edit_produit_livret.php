<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;

$lettre = CValue::get("lettre");
$codeATC = CValue::get("codeATC");
$code_cip = CValue::get("code_cip");

// Chargement du produit
$produit_livret = new CBcbProduitLivretTherapeutique();
$produit_livret->load($code_cip);
$produit_livret->loadRefProduit();

$produit_livret->updateFormFields();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("code_cip", $code_cip);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("lettre", $lettre);
$smarty->assign("produit_livret", $produit_livret);

$smarty->display("edit_produit_livret.tpl");

?>