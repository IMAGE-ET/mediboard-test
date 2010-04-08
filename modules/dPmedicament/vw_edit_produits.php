<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if(!CModule::getActive('bcb')){
  CAppUI::stepMessage(UI_MSG_ERROR, "Le module de médicament autonome est en cours de developpement. 
  Pour être utilisé, ce module a pour le moment besoin d'être connecté à une base de données de médicaments externe");
  return;
}

$produit_id = CValue::getOrSession("produit_prescription_id");

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