<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$tokens              = CValue::post("produit", "aaa");
$inLivret            = CValue::post("inLivret", 0);
$produit_max         = CValue::post("produit_max", 10);
$search_libelle_long = CValue::post("search_libelle_long", false);
$hors_specialite     = CValue::post("hors_specialite", "0");
$search_by_cis       = CValue::post("search_by_cis", "1");
$fast_access         = CValue::post("fast_access", "0");
$praticien_id        = CValue::post("praticien_id");

$mbProduit = new CBcbProduit();

// Recherche dans la bcb
$search_by_name = $mbProduit->searchProduitAutocomplete($tokens, "50", $inLivret, $search_libelle_long, $hors_specialite, $search_by_cis);

// Recherche des produits en se basant sur les DCI
$dci = new CBcbDCI();

if($inLivret){
	$dci->distObj->LivretTherapeutique = CGroups::loadCurrent()->_id;
}

if(!$hors_specialite){
  $search_by_dci = $dci->searchProduits($tokens, 100, $search_by_cis);
} else {
	$search_by_dci = array();
}

$produits = array();
foreach($search_by_name as $key => $_produit){
	$produits[$key] = $_produit;
}

if(!$hors_specialite){
	foreach($search_by_dci as $key => $_produit){
	  if(!array_key_exists($key, $produits)){
	    $produits[$key] = $_produit;
		}
	}
}

$protocoles = array();
// Recherche des protocoles qui possedent le code CIP du produit trouvé par l'autocomplete
if($fast_access){
	// Chargement du praticien
	$praticien = new CMediusers();
	$praticien->load($praticien_id);
	$praticien->loadRefFunction();
	
	// Chargement des protocoles fast access
	$prot_fast_access = new CPrescription();
	$where = array();
	$where["fast_access"] = " = '1'";
  $where["object_id"] = " IS NULL";
	$where[] = "praticien_id = '$praticien_id' OR function_id = '$praticien->function_id' OR group_id = '{$praticien->_ref_function->group_id}'";
	$protocoles_id = $prot_fast_access->loadIds($where);
	
  foreach($produits as $_produit){
		$prescription = new CPrescription();
		$ljoin = array();
		$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
		$ljoin["prescription_line_mix"]        = "prescription_line_mix.prescription_id = prescription.prescription_id";
    $ljoin["prescription_line_mix_item"]   = "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";
    
		$where = array();
		$where[] = "prescription_line_medicament.code_cip = '$_produit->CodeCIP' OR prescription_line_mix_item.code_cip = '$_produit->CodeCIP'";
    $where["prescription.prescription_id"] = CSQLDataSource::prepareIn($protocoles_id);
		
		$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
		
    foreach($prescriptions as $_presc){
      $_presc->countLinesMedsElements();
      foreach($_presc->_counts_by_chapitre as $chapitre => $_count_chapitre){
        if(!$_count_chapitre){
          unset($_presc->_counts_by_chapitre[$chapitre]);
        }
      }
	  }
		$protocoles[$_produit->CodeCIP] = $prescriptions;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("produits", $produits);
$smarty->assign("protocoles", $protocoles);
$smarty->assign("nodebug", true);
$smarty->assign("search_libelle_long", $search_libelle_long);
$smarty->assign("tokens", $tokens);
$smarty->assign("search_by_cis", $search_by_cis);
$smarty->assign("fast_access", $fast_access);
$smarty->display("httpreq_do_medicament_autocomplete.tpl");

?>