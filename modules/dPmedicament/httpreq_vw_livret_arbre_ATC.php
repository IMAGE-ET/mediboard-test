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
	// dans le cas d'un ajout de produit, on position l'arbre sur le produit ajouté
	// Chargement de la premiere classe ATC a laquelle le produit appartient
	$mbProduitThera = new CProduitLivretTherapeutique();
	$mbProduitThera->code_cip = $code_cip;
	$mbProduitThera->loadMatchingObject();
	//Chargement des classes ATC du produit
	$mbProduitThera->loadRefProduit();
	$mbProduitThera->_ref_produit->loadClasseATC();
	foreach($mbProduitThera->_ref_produit->_ref_classes_ATC as $key => $classeATC){
	  $codeATC = $classeATC->Code;
	}
}

$listProduits = array();

// Chargement de l'etablissement courant
$etablissement = new CGroups();
$etablissement->load($g);

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

$produits_livret = $etablissement->_ref_produits_livret;

// Chargement de tous les produits du livret therapeutique
foreach($produits_livret as $key => $_produit_livret){
  $_produit_livret->loadRefProduit();
  $produits[$_produit_livret->code_cip] = $_produit_livret->_ref_produit;
}

// Chargement des classes ATC des produits
foreach($produits as $key => $_produit){
  $_produit->loadClasseATC();
}
if($codeATC){
	// Stockage des codes ATC de chaque produit
	foreach($produits as $key => $produit){
	  foreach($produit->_ref_classes_ATC as $key => $nbclasseATC){
	    $classeProduits[$produit->code_cip][] = $nbclasseATC->Code; 
	    $tailleCodeATC = strlen($codeATC);
	    if(substr($nbclasseATC->Code, 0, $tailleCodeATC) == $codeATC){
	      $produit_livret = new CProduitLivretTherapeutique();
	      $produit_livret->code_cip = $produit->code_cip;
	      $produit_livret->loadMatchingObject();
	      $listProduits[$produit->code_cip] = $produit_livret;
	      $produit_livret->loadRefProduit();   
	    }
	  }
	}
}



// Creation de l'arbre
$classeATC = new CBcbClasseATC();
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