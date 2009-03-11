<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author Alexis Granger
 */

global $can;
$can->needsAdmin();

$category = new CProductCategory();
$categories_list = $category->loadList(null, 'name');

$nb_produit_livret = CBcbProduitLivretTherapeutique::countProduits();
$nb_produit_livret_bcbges = CBcbProduitLivretTherapeutique::countProduitsBCBGES();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('categories_list', $categories_list);
$smarty->assign('nb_produit_livret', $nb_produit_livret);
$smarty->assign('nb_produit_livret_bcbges', $nb_produit_livret_bcbges);
$smarty->display("configure.tpl");

?>