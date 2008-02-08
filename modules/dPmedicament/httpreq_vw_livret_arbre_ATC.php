<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $g;

$listCodeATC = array();

$codeATC  = mbGetValueFromGet("codeATC");
$code_cip = mbGetValueFromGet("code_cip");

if($code_cip){
  $mbProduitThera = new CBcbProduitLivretTherapeutique();
  $mbProduitThera->load($code_cip);
  $mbProduitThera->loadRefProduit();
  $mbProduitThera->_ref_produit->loadClasseATC();
  foreach($mbProduitThera->_ref_produit->_ref_classes_ATC as $key => $classeATC){
    $codeATC = $classeATC->Code;
  }
}

// Creation de l'arbre
$classeATC = new CBcbClasseATC();
// Chargement des produits du livret 
$listProduits = $classeATC->loadRefProduitsLivret($codeATC);
// Nom du chapitre selectionne
$chapitreATC = $classeATC->getLibelle($codeATC);
// Chargements des sous chapitres
$arbreATC = $classeATC->loadArbre($codeATC);
// Calcul du niveau du code
$niveauCodeATC = $classeATC->getNiveau($codeATC);
// Calcul du code de niveau superieur
$codeNiveauSup = $classeATC->getCodeNiveauSup($codeATC);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listProduits", $listProduits);
$smarty->assign("codeNiveauSup", $codeNiveauSup);
$smarty->assign("niveauCodeATC", $niveauCodeATC);
$smarty->assign("chapitreATC", $chapitreATC);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("arbreATC", $arbreATC);
$smarty->assign("classeATC", $classeATC);

$smarty->display("inc_vw_livret_arbre_ATC.tpl");


?>