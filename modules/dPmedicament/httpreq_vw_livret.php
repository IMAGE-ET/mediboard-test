<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

// Cas d'ajout de produit, affichage de la bonne lettre
$lettre = mbGetValueFromGet("lettre");
$code_cip = mbGetValueFromGetOrSession("code_cip");
$type = mbGetValueFromGet("type");

// Chargement du produit ajouté
if($code_cip){	
  $mbProduit = new CBcbProduit();
	$mbProduit->load($code_cip);
	$lettre = substr($mbProduit->libelle, 0, 1); 	
}

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique($lettre);

$tabLettre = range('A', 'Z');


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tabLettre", $tabLettre);
$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);
$smarty->assign("lettre", $lettre);

$smarty->display("inc_vw_livret.tpl");

?>