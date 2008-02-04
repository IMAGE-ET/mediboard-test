<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $g;


// Cas d'ajout de produit, affichage de la bonne lettre
$lettre = mbGetValueFromGet("lettre");
$code_cip = mbGetValueFromGet("code_cip");
$type = mbGetValueFromGet("type");

// Chargement du produit ajouté
if($code_cip){	
  $mbProduit = new CBcbProduit();
	$mbProduit->load($code_cip);
	$lettre = substr($mbProduit->libelle, 0, 1); 	
}

// Chargement de l'etablissement courant
$etablissement = new CGroups();
$etablissement->load($g);

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique($lettre);

// Chargement du produit
foreach($etablissement->_ref_produits_livret as $key => $produit_livret){
  $produit_livret->loadRefProduit();
}

$tabLettre = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Z");


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tabLettre", $tabLettre);
$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);
$smarty->assign("lettre", $lettre);

$smarty->display("inc_vw_livret.tpl");


?>