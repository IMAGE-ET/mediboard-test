<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $AppUI;
$can->needsAdmin();

set_time_limit(360);
ini_set('memory_limit', '128M');

$category_id     = CValue::get('category_id');

$category = new CProductCategory();
if (!$category->load($category_id)) {
  CAppUI::stepAjax('Veuillez choisir une catégorie de produits correspondant au livret thérapeutique de l\'établissement', UI_MSG_ERROR);
  return;
}

$messages = array();

// Chargement du livret thérapeutique de l'établissement
$group = CGroups::loadCurrent();
$group->loadRefLivretTherapeutique('%', 1000, false);

// Chargement des produits du livret thérapeutique
foreach ($group->_ref_produits_livret as $produit_livret) {

	$produit_livret->_ref_produit->loadConditionnement();
	$produit_livret->_ref_produit->loadLibellePresentation();
	
	// Recherche du produit dans la table de produits hors AMM
	$produit_prescription = new CProduitPrescription();
	$produit_prescription->code_cip = $produit_livret->code_cip;
	$produit_prescription->loadMatchingObject();
  
  if($produit_prescription->_id){
    $libelle = $produit_prescription->libelle;
    $quantite = $produit_prescription->nb_presentation;
    
    $libelle_presentation = $produit_prescription->unite_dispensation;
    $nb_unite_presentation = $produit_prescription->quantite; 
    $libelle_unite_presentation = $produit_prescription->unite_prise;
    $packaging = "";
  } else {    
    $_produit =& $produit_livret->_ref_produit; 
    $libelle = $_produit->libelle;
    $packaging = $_produit->libelle_conditionnement;  
    
    if($_produit->libelle_presentation){
      $quantite = $_produit->nb_presentation;
      $libelle_presentation = $_produit->libelle_presentation;
      $nb_unite_presentation = $_produit->nb_unite_presentation ? $_produit->nb_unite_presentation : 1;
      $libelle_unite_presentation = $_produit->libelle_unite_presentation;
    } else {
      $quantite = $_produit->nb_unite_presentation;
      $libelle_presentation = $_produit->libelle_unite_presentation;
      $nb_unite_presentation = "";
      $libelle_unite_presentation = "";
    }
  }
  
  $product = new CProduct();
  $product->code          = $produit_livret->code_cip;
  
  if (!$product->loadMatchingObject()) {
    $product->category_id = $category_id;
    $product->name        = $libelle;
  }
  
  $product->description   = $produit_livret->commentaire;
  $product->packaging     = $packaging;
  $product->quantity      = $quantite;
  $product->item_title    = $libelle_presentation;
  $product->unit_quantity = $nb_unite_presentation;
  $product->unit_title    = $libelle_unite_presentation;
  
  if($product->item_title == $product->unit_title){
  	$product->item_title = "";
  }
  
  // On vérifie si le fabriquant du produit est déjà dans la base de données
  if ($produit_livret->_ref_produit->nom_laboratoire) {
    $societe = new CSociete();
    $societe->name = $produit_livret->_ref_produit->nom_laboratoire;
    if (!$societe->loadMatchingObject()) {
      $societe->store();
      $msg = 'Société ajoutée';
      if (!isset($messages[$msg])) $messages[$msg] = 0;
      $messages[$msg]++;
    }
    $product->societe_id = $societe->_id;
  }

  $msg = $product->store();

  // Sauvegarde du nouveau produit correspondant au médicament
  if (!$msg) {
  	$product->updateFormFields();
  	
    $stock = new CProductStockGroup();
    $stock->product_id = $product->_id;
    $stock->group_id = $group->_id;
    if (!$stock->loadMatchingObject()) {
	    $stock->quantity = $product->_unit_quantity;
	    $stock->order_threshold_min = $stock->quantity;
	    //$stock->order_threshold_max = $stock->quantity * 2;
	    if ($msg = $stock->store()) {
	    	if (!isset($messages[$msg])) $messages[$msg] = 0;
	      $messages[$msg]++;
	    } else {
	    	$msg = 'Stock produit ajouté';
	      if (!isset($messages[$msg])) $messages[$msg] = 0;
	      $messages[$msg]++;
	    }
    }
  } else {
    $msg .= " ($product->code: $product->name)";
    if (!isset($messages[$msg])) $messages[$msg] = 0;
    $messages[$msg]++;
  }
}

foreach ($messages as $msg => $count) {
	CAppUI::stepAjax("$msg x $count", UI_MSG_ALERT);
}
CAppUI::stepAjax('Synchronisation des produits terminée', UI_MSG_OK);

// Sauvegarde de la catégorie en variable de config
$conf = new CMbConfig();
$data = array();
$data['dPmedicament']['CBcbProduitLivretTherapeutique']['product_category_id'] = $category_id;
if ($conf->update($data, true)) {
  CAppUI::stepAjax('Enregistrement de la catégorie de produits effectuée', UI_MSG_OK);
}

?>