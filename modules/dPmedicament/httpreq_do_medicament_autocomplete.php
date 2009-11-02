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
$hors_specialite = mbGetValueFromPost("hors_specialite", "0");
$search_by_cis = mbGetValueFromPost("search_by_cis", "1");

$mbProduit = new CBcbProduit();

// Recherche dans la bcb
$search_by_name = $mbProduit->searchProduitAutocomplete($produit, $produit_max, $inLivret, $search_libelle_long, $hors_specialite, $search_by_cis);

// Recherche des produits en se basant sur les DCI
$dci = new CBcbDCI();

if($inLivret){
	$dci->distObj->LivretTherapeutique = CGroups::loadCurrent()->_id;
}
$search_by_dci = $dci->searchProduits($produit, 100, $search_by_cis);

$produits = array();
foreach($search_by_name as $key => $_produit){
	$produits[$key] = $_produit;
}
foreach($search_by_dci as $key => $_produit){
  $produits[$key] = $_produit;
}


// Classement des lignes par ordre alphabetique
function compareMed($produit1, $produit2){
  return strcmp($produit1->Libelle, $produit2->Libelle);
}
if(isset($produits)){
  usort($produits, "compareMed");
}


// Tableau de tokens permettant de les mettre en evidence dans l'autocomplete
$_tokens = explode(" ", $produit);
foreach($_tokens as $_token){
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