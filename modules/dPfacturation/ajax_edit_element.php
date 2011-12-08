<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//recuperer avec get la variable $factureitem_id
$factureitem_id = CValue::getOrSession("factureItem_id");
$facture_id = CValue::getOrSession("facture_id");
$catalogue_item_id = CValue::get("catalogue_item_id");

$factureitem = new CFactureItem();
$catalogue_item = new CFacturecatalogueitem();	

if ($catalogue_item_id) {
	//$catalogue_item = new CFacturecatalogueitem();
	$catalogue_item->load($catalogue_item_id);
	
	$factureitem->prix_ht = $catalogue_item->prix_ht;
	$factureitem->taxe = $catalogue_item->taxe;
	$factureitem->libelle = $catalogue_item->libelle;
	$factureitem->facture_catalogue_item_id = $catalogue_item->_id;
	
	//
}
else {
	// Chargement des éléments d'une facture demandé
	$factureitem->load($factureitem_id);
	$factureitem->loadRefsFwd();
}

if ($factureitem->_id) {
	$facture = $factureitem->_ref_facture;
}
else {
	$facture = new CFacture();
	$facture->load($facture_id);
}
$listCatalogueItem = $catalogue_item->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);
$smarty->assign("factureitem", $factureitem);
$smarty->assign("listCatalogueItem", $listCatalogueItem);//
$smarty->display("inc_edit_element.tpl");

?>