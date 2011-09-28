<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Cas d'ajout de produit, affichage de la bonne lettre
$lettre         = CValue::get("lettre");
$code_cip       = CValue::getOrSession("code_cip");
$type           = CValue::get("type");
$function_guid  = CValue::get("function_guid", null);
$produits_livret = array();

// Chargement du produit ajouté
if($code_cip){	
  $mbProduit = new CBcbProduit();
	$mbProduit->load($code_cip);
	$lettre = substr($mbProduit->libelle_long, 0, 1); 	
}

if($lettre == "hors_T2A"){
  $produits_livret = CBcbProduit::getHorsT2ALivret($function_guid);
} else {
	// Chargement des produits du livret therapeutique
	$produits_livret = CBcbProduit::loadRefLivretTherapeutique($function_guid, $lettre, 200);
}

$tabLettre = range('A', 'Z');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tabLettre", $tabLettre);
$smarty->assign("produits_livret", $produits_livret);
$smarty->assign("lettre", $lettre);
$smarty->assign("function_guid", $function_guid);

$smarty->display("inc_vw_livret.tpl");

?>