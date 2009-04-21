<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Alexis Granger
*/


$DC_search = mbGetValueFromGet("DC_search", "");
$DCI_code = mbGetValueFromGet("DCI_code", "");
$dialog = mbGetValueFromGet("dialog");
$forme = mbGetValueFromGet("forme");
$dosage = mbGetValueFromGet("dosage");
$rechercheLivretDCI = mbGetValueFromGet("rechercheLivretDCI", 0);


$DCI = new CBcbDCI();
$tabDCI = array();
$tabProduit = array();
$tabViewProduit = array();

if($DC_search){
  $tabDCI = $DCI->searchDCI($DC_search);
}

if($DCI_code){
  // Chargement de la DCI
  $DCI->load($DCI_code);
  // Chargement des produits de la DCI
  $DCI->searchProduitsByType($rechercheLivretDCI);
  foreach($DCI->_ref_produits as $key => $_produit){
    // Tri par forme et dosage
    $tabProduit[$_produit->Dosage][$_produit->Forme][] = $_produit;
  }
  if($forme && $dosage){
    $tabViewProduit = $tabProduit[$dosage][$forme];
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rechercheLivretDCI", $rechercheLivretDCI);
$smarty->assign("tabViewProduit", $tabViewProduit);
$smarty->assign("dialog", $dialog);
$smarty->assign("tabProduit", $tabProduit);
$smarty->assign("tabDCI", $tabDCI);
$smarty->assign("DC_search", $DC_search);
$smarty->assign("DCI_code", $DCI_code);
$smarty->assign("DCI", $DCI);

$smarty->display("inc_vw_DCI.tpl");

?>