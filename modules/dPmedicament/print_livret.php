<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$orderby = CValue::get("orderby", "libelle");
$function_guid = CValue::get("function_guid", null);

$produits_livret = array();

// Chargement des produits du livret therapeutique
$produit_livret = new CProduitLivretTherapeutique();

$crc = isset($function_guid) ? $function_guid : CProductStockGroup::getHostGroup(false)->_guid;
$crc = CBcbProduit::getHash($crc);

$produit_livret->owner_crc = $crc;
$produits_livret_temp = $produit_livret->loadMatchingList();

foreach($produits_livret_temp as $_produit_livret){
	$_produit_livret->loadRefProduit();
  $_produit_livret->_ref_produit->isInT2A();
  $_produit = $_produit_livret->_ref_produit;
  
  $_produit->loadClasseATC();
  $classe_atc = reset($_produit->_ref_classes_ATC);
	
  if(isset($classe_atc->classes)){
	  $classe_atc = end($classe_atc->classes);
	  $_produit_livret->_atc = $classe_atc["code"];
	} else {
		$_produit_livret->_atc = "";
	}
	  
  if ($orderby === "libelle")
    $produits_livret["$_produit->libelle-$_produit->code_cip"] = $_produit_livret;
  else {
    $produits_livret["$_produit_livret->_atc-$_produit->libelle-$_produit->code_cip"] = $_produit_livret;
  }
}

// Tri par ordre alphabetique du livret therapeutique
ksort($produits_livret);
  
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", mbDate());
$smarty->assign("produits_livret", $produits_livret);

$smarty->display("print_livret.tpl");

?>