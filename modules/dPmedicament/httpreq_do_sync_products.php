<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Fabien Ménager
*/

global $can, $g;

$can->needsAdmin();

set_time_limit(360);
ini_set('memory_limit', '128M');

$category_id = mbGetValueFromGet('category_id');
$category = new CProductCategory();
if (!$category_id || !$category->load($category_id)) {
  CAppUI::stepAjax('Veuillez choisir une catégorie de produits correspondant au livret thérapeutique de l\'établissement', UI_MSG_ERROR);
  return;
}

// Chargement du livret thérapeutique de l'établissement
$group = new CGroups();
$group->load($g);
$group->loadRefLivretTherapeutique();

// Chargement des produits du livret thérapeutique
foreach ($group->_ref_produits_livret as $produit_livret) {
  $product = new CProduct();
  $prod = $produit_livret->_ref_produit;
  $product->name = $prod->libelle;
  $product->description = $produit_livret->commentaire;
  $product->code = $produit_livret->code_cip;
  $product->category_id = $category_id;
  
  // Sauvegarde du nouveau produit correspondant au médicament
  if (!($msg = $product->store())) {
    $stock = new CProductStockGroup();
    $stock->product_id = $product->_id;
    $stock->group_id = $g;
    $stock->quantity = 1;
    $stock->order_threshold_min = 1;
    $stock->order_threshold_max = 1;
    if ($msg = $stock->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ALERT);
    }
  } else {
    CAppUI::stepAjax($msg, UI_MSG_ALERT);
  }
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