<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

set_time_limit(360);
CMbObject::$useObjectCache = false;

// Recuperation du fichier
$file = CValue::read($_FILES, "datafile");

$ignored = array(
  array(0, "DATE:"),
  array(0, "PAGE:"),
  array(0, "Famille"),
  array(0, "Sous Famille"),
  array(0, "Désignation Article"),
  
  "CSociete" => array(0, "FOURNISSEUR"),
  
  array(1, "Cumul Article"),
  array(1, "Cumul Sous-Famille"),
  array(1, "Cumul Famille"),
);

$delim = ",";

$map = array(
  0 => "",
);

if (strtolower(CMbPath::getExtension($file["name"]) != 'csv')) {
  CAppUI::setMsg("Le fichier doit être de type CSV", UI_MSG_ERROR);
  return;
}

$dmi_category = new CDMICategory;
$dmi_category->loadMatchingObject();
if (!$dmi_category->_id) {
  CAppUI::setMsg("Au moins une catégorie de DMI doit exister", UI_MSG_ERROR);
  return;
}

$csv = fopen($file["tmp_name"], 'r');

$current_supplier = null;
$current_product = null;

$n = 10;
while ((($data = fgetcsv($csv, null, $delim)) !== false)/* && $n--*/) {
  $data = array_map("trim", $data);
  
  // Ignored lines
  foreach($ignored as $_class => $_ignored) {
    if (strpos($data[$_ignored[0]], $_ignored[1]) === 0) {
      
      // Supplier
      if ($_class === "CSociete") {
        $current_supplier = new CSociete;
        $current_supplier->name = $current_supplier->_spec->ds->escape($data[1]);
        $current_supplier->loadMatchingObject();
      }
      
      continue 2; // we continue in the while loop
    }
  }
  
  // Continue if no supplier
  if (!$current_supplier || !$current_supplier->_id) {
    CAppUI::setMsg("Fournisseur non retrouvé");
    continue;
  }
  
  // Product
  if ($data[0]) {
    if (!$data[1]) {
      CAppUI::setMsg("Le produit <strong>{$data[0]}</strong> n'a pas de code, il ne peut pas être importé", UI_MSG_WARNING);
      continue;
    }
    
    // PRODUIT
    $current_product = new CProduct;
    $current_product->code = $current_product->_spec->ds->escape($data[1]);
    
    // match CProduct
    if (!$current_product->loadMatchingObject()) {
      $current_product->name = $data[0];
      $current_product->code = $data[1];
      $current_product->quantity = 1;
      $current_product->societe_id = $current_supplier->_id;
      $current_product->category_id = CAppUI::conf("dmi CDMI product_category_id");
      
      if ($msg = $current_product->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
        continue;
      }
      else {
        CAppUI::setMsg("CProduct-msg-create", UI_MSG_OK);
      }
    }
  }
  
  if (!$current_product || !$current_product->_id) {
    CAppUI::setMsg("Produit non retrouvé");
    continue;
  }
  
  // DMI
  $dmi = CDMI::getProduct($current_product->code);
  
  // match CDMI
  if (!$dmi->_id) {
    $dmi->code = $current_product->code;
    $dmi->nom = $current_product->name;
    $dmi->category_id = $dmi_category->_id;
    $dmi->in_livret = 1;
    if ($msg = $dmi->store()){
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
    else {
      CAppUI::setMsg("CDMI-msg-create", UI_MSG_OK);
    }
  }
  
  // REFERENCE
  $reference = new CProductReference;
  $reference->product_id = $current_product->_id;
  $reference->societe_id = $current_supplier->_id;
  
  // match CProductReference
  if (!$reference->loadMatchingObject()) {
    $reference->quantity = 1;
    $reference->price = 0;
    if ($msg = $reference->store()){
      CAppUI::setMsg($msg, UI_MSG_WARNING);
      continue;
    }
  }

  // LOT
  $item_reception = new CProductOrderItemReception;
  $item_reception->code = $data[2];
  $item_reception->quantity = $data[3];
  $item_reception->date = mbDateFromLocale($data[4])." 00:00:00";
  
  if ($data[5] !== "31/12/1899") {
    $item_reception->lapsing_date = mbDateFromLocale($data[5]);
  }
  
  // match CProductOrderItemReception
  if (!$item_reception->loadMatchingObject()) {
    $order_item = new CProductOrderItem;
    $order_item->reference_id = $reference->_id;
    $order_item->quantity = 1;
    
    if ($msg = $order_item->store()){
      CAppUI::setMsg($msg, UI_MSG_WARNING);
      continue;
    }
  
    $item_reception->order_item_id = $order_item->_id;
  }
  
  $reception_is_new = !$item_reception->_id;
  if ($msg = $item_reception->store()){
    CAppUI::setMsg($msg, UI_MSG_WARNING);
    continue;
  }
  
  if ($reception_is_new)
    CAppUI::setMsg("CProductOrderItemReception-msg-create", UI_MSG_OK);
  else
    CAppUI::setMsg("Réception retrouvée", UI_MSG_OK);
  //break;
}

fclose($csv);
