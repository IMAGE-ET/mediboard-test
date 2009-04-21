<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Recuperation du CIP du produit
$cip = mbGetValueFromGetOrSession("CIP");

$mbProduit = CBcbProduit::get($cip, true);

if($mbProduit->code_cip){
	// Chargement des donnees technico-reglementaires
	$mbProduit->loadRefEconomique();
	// Chargement des classes ATC du produit
	$mbProduit->loadClasseATC();
	// Chargement des classes therapeutiques du produit
	$mbProduit->loadClasseTherapeutique();
}

// Creation du template
$smarty = new CSmartyDP();
$smarty->assign("mbProduit", $mbProduit);
$smarty->display("vw_produit.tpl");

?>