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
  array(0, "Date:"),
  array(0, "Analyse pour le Stock"),
  array(0, "Analyse selon la Classification"),
  "CDMICategory" => array(0, "Famille :"),
  array(0, "PAGE:"),
  array(0, "Stock"),
  array(0, "Famille"),
  array(0, "Désignation article"),
);

$delim = ",";

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

$current_product  = null;
$current_category = null;

$n = 10;
while ((($data = fgetcsv($csv, null, $delim)) !== false)/* && $n--*/) {
  $data = array_map("trim", $data);
  
  // Ignored lines
  foreach($ignored as $_class => $_ignored) {
    if (strpos($data[$_ignored[0]], $_ignored[1]) === 0) {
      
      if ($_class === "CDMICategory") {
        $nom_famille = substr($data[0], 10);
        $current_category = new CDMICategory;
        $current_category->nom = $current_category->_spec->ds->escape($nom_famille);
        $current_category->group_id = CGroups::loadCurrent()->_id;
        $current_category->loadMatchingObject();
        if($msg = $current_category->store()) {
          CAppUI::setMsg($msg, UI_MSG_WARNING);
        }
      }
      
      continue 2; // we continue in the while loop
    }
  }
  
  // Continue if no category
  if (!$current_category || !$current_category->_id) {
    CAppUI::setMsg("Catégorie non retrouvée");
    continue;
  }
  
  // Product
  if ($data[0]) {
    if (!$data[1]) {
      CAppUI::setMsg("Le produit <strong>{$data[0]}</strong> n'a pas de code, il ne peut pas être retrouvé", UI_MSG_WARNING);
      continue;
    }
    
    // PRODUIT
    $current_product = new CProduct;
    $current_product->code = $current_product->_spec->ds->escape($data[1]);
    
    // match CProduct
    if (!$current_product->loadMatchingObject()) {
      //CAppUI::setMsg("Produit <strong>$data[0]</strong> non retrouvé" , UI_MSG_OK);
      continue;
    }
  }
  
  // DMI
  $dmi = CDMI::getFromProduct($current_product);
  if (!$dmi->_id) {
    CAppUI::setMsg("DMI <strong>$data[0]</strong> non retrouvé" , UI_MSG_OK);
    continue;
  }
  
  $dmi->category_id = $current_category->_id;
  if ($data[2]) {
    $dmi->code_lpp = $data[2];
  }
  if ($msg = $dmi->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  
  // REFERENCE
  $reference = new CProductReference;
  $reference->product_id = $current_product->_id;
  
  // match CProductReference
  if (!$reference->loadMatchingObject()) {
    CAppUI::setMsg("Référence pour <strong>$data[0]</strong> non retrouvée" , UI_MSG_OK);
    continue;
  }

  $reference->price = str_replace(",", ".", $data[3]);
  if ($msg = $reference->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
}

fclose($csv);
