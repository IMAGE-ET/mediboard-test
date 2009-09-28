<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$produit_id = mbGetValueFromGetOrSession("produit_prescription_id");

// Chargement du produit selectionné
$produit = new CProduitPrescription();
$produit->load($produit_id);

// Chargement de tous les produits redefinis
$produits = $produit->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("produit", $produit);
$smarty->assign("produits", $produits);
$smarty->display("vw_edit_produits.tpl");


?>