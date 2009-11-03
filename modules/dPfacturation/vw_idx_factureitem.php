<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI, $can, $m;

$can->needsRead();

// Chargement de l'item choisi
$libelleItem = new CFactureItem;
$libelleItem->loadAides($AppUI->user_id);

// Reception de l'id de la facture a partir de l'url
$facture_id = CValue::getOrSession("facture_id");
$factureitem_id = CValue::get("factureitem_id");


// Chargement de la facture demandé
$facture = new CFacture();
$facture->load($facture_id);

// Chargement des éléments d'une facture demandé
$factureitem = new CFactureitem();
$factureitem->load($factureitem_id);

// Chargement des donnees de la facture
$facture->loadRefs();

// Récupération de la liste des factures
$itemFacture = new CFacture;
$listFacture = $itemFacture->loadList();
foreach($listFacture as $curr_facture) {
  $curr_facture->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);
$smarty->assign("factureitem", $factureitem);
$smarty->assign("listFacture", $listFacture);
$smarty->assign("libelleItem", $libelleItem);
$smarty->display("vw_idx_factureitem.tpl");
?>