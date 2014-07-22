<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();
$see    = CValue::get("see", 1);
$debut  = CValue::get("debut", CMbDT::date());
$fin    = CValue::get("fin", CMbDT::date());

$fact_item = new CFactureItem();
$ds = $fact_item->_spec->ds;
$query = "SELECT f.*
  FROM `factureitem` f, `factureitem` c
  WHERE f.factureitem_id <> c.factureitem_id
  AND f.object_id = c.object_id
  AND f.object_class = c.object_class
  AND f.date = c.date
  AND f.libelle = c.libelle
  AND f.type = c.type
  AND f.montant_base = c.montant_base
  AND f.montant_depassement = c.montant_depassement
  AND f.quantite = c.quantite
  AND f.date BETWEEN '".$debut."' AND '".$fin."'
";
$items = $ds->loadList($query);

$items_to_delete = array();
$factures = array();
foreach ($items as $_item) {
  $factures[$_item["object_class"]."-".$_item["object_id"]] = true;
  if (!isset($items_to_delete[$_item["factureitem_id"]])) {
    $items_to_delete[$_item["factureitem_id"]] = $_item;
  }
}

$facture_not_load = array();
$items = array();
foreach ($items_to_delete as $_item_see) {
  if (!isset($facture_not_load[$_item_see["object_class"]."-".$_item_see["object_id"]])) {
    $item = new CFactureItem();
    $item->load($_item_see["factureitem_id"]);
    $facture = null;
    $facture = $item->loadRefFacture();
    $facture->_ref_actes_ngap = array();
    $facture->_ref_actes_ccam = array();
    $facture->_ref_actes_divers = array();
    $facture->loadRefsConsultation();
    $actes = count($facture->_ref_actes_ngap) + count($facture->_ref_actes_ccam) + count($facture->_ref_actes_divers);
    $facture->loadRefsItems();

    if (count($facture->_ref_items) == $actes) {
      $facture_not_load[$_item_see["object_class"]."-".$_item_see["object_id"]] = true;
      unset($factures[$_item_see["object_class"]."-".$_item_see["object_id"]]);
      unset($items_to_delete[$_item_see["factureitem_id"]]);
    }
    else {
      $items[] = $item;
    }
  }
  else {
    unset($items_to_delete[$_item_see["factureitem_id"]]);
  }
}

$items_delete = 0;
if (!$see) {
  //Suppression des lignes en trop
  foreach ($items_to_delete as $_item) {
    $item = new CFactureItem();
    if ($item->load($_item["factureitem_id"])) {
      $where = array();
      $where["factureitem_id"]= " != '".$_item["factureitem_id"]."'";
      $where["object_id"]     = " = '".$_item["object_id"]."'";
      $where["object_class"]  = " = '".$_item["object_class"]."'";
      $where["date"]          = " = '".$_item["date"]."'";
      $where["type"]          = " = '".$_item["type"]."'";
      $where["montant_base"]  = " = '".$_item["montant_base"]."'";
      $where["montant_depassement"] = " = '".$_item["montant_depassement"]."'";
      $where["quantite"]      = " = '".$_item["quantite"]."'";
      $new_item = new CFactureItem();
      $new_items = $new_item->loadList($where);
      if (count($new_items)) {
        foreach ($new_items as $_dell_item) {
          if ($msg = $_dell_item->delete()) {
            echo $msg;
          }
          else {
            $items_delete++;
          }
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"    , $factures);
$smarty->assign("items"       , $items);
$smarty->assign("items_delete", $items_delete);
$smarty->assign("see"         , $see);
$smarty->assign("debut"       , $debut);
$smarty->assign("fin"         , $fin);

$smarty->display("inc_configure_actions.tpl");