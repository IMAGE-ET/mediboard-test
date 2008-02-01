<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */



global $AppUI, $can, $m, $g;

// Chargement de l'etablissement courant
$etablissement = new CGroups();
$etablissement->load($g);

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

// Chargement du produit
foreach($etablissement->_ref_produits_livret as $key => $produit_livret){
  $produit_livret->loadRefProduit();
}



// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);

$smarty->display("vw_idx_livret.tpl");

?>