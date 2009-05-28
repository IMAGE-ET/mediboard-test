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

// Tableau de tokens permettant de les mettre en evidence dans l'autocomplete
$_tokens = explode(" ", $produit);
foreach($_tokens as $_token){
  $token_search[] = $_token;
  $token_replace[] = "<em>".$_token."</em>";

  $_token = strtoupper($_token);
  $token_search[] = $_token;
  $token_replace[] = "<em>".$_token."</em>";
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("produits", $produits);
$smarty->assign("nodebug", true);
$smarty->assign("search_libelle_long", $search_libelle_long);
$smarty->assign("needle", strtoupper($produit));
$smarty->assign("search_by_cis", $search_by_cis);
$smarty->assign("token_search", $token_search);
$smarty->assign("token_replace", $token_replace);
$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>