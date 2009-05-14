<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$produit = mbGetValueFromPost("produit", "aaa");
$inLivret = mbGetValueFromPost("inLivret", 0);
$produit_max = mbGetValueFromPost("produit_max", 10);
$search_libelle_long = mbGetValueFromPost("search_libelle_long", false);
$specialite = mbGetValueFromPost("specialite", "1");
$search_by_cis = mbGetValueFromPost("search_by_cis", "1");

$mbProduit = new CBcbProduit();

// Recherche dans la bcb
$produits = $mbProduit->searchProduitAutocomplete($produit, $produit_max, $inLivret, $search_libelle_long, $specialite, $search_by_cis);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("produits", $produits);
$smarty->assign("nodebug", true);
$smarty->assign("search_libelle_long", $search_libelle_long);
$smarty->assign("needle", strtoupper($produit));
$smarty->assign("search_by_cis", $search_by_cis);
$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>