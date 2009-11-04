<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$tokens             = CValue::post("produit", "aaa");
$inLivret            = CValue::post("inLivret", 0);
$produit_max         = CValue::post("produit_max", 10);
$search_libelle_long = CValue::post("search_libelle_long", false);
$hors_specialite     = CValue::post("hors_specialite", "0");
$search_by_cis       = CValue::post("search_by_cis", "1");

$mbProduit = new CBcbProduit();

// Recherche dans la bcb
$search_by_name = $mbProduit->searchProduitAutocomplete($tokens, $produit_max, $inLivret, $search_libelle_long, $hors_specialite, $search_by_cis);

// Recherche des produits en se basant sur les DCI
$dci = new CBcbDCI();

if($inLivret){
	$dci->distObj->LivretTherapeutique = CGroups::loadCurrent()->_id;
}
$search_by_dci = $dci->searchProduits($tokens, 100, $search_by_cis);

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

if (isset($produits)){
  usort($produits, "compareMed");
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("produits", $produits);
$smarty->assign("nodebug", true);
$smarty->assign("search_libelle_long", $search_libelle_long);
$smarty->assign("tokens", $tokens);
$smarty->assign("search_by_cis", $search_by_cis);
$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>