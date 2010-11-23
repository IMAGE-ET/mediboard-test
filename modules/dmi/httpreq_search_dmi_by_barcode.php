<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$barcode = CValue::get("barcode");

CValue::setSession("barcode", $barcode);

$parsed = CBarcodeParser::parse($barcode);
$comp = $parsed["comp"];

$dmi_category_id = CAppUI::conf("dmi CDMI product_category_id");

$object = new CProduct;

$keys = array("scc_prod", "ref", "cip", "raw");
$values = array_intersect_key($comp, array_flip($keys));
$products = array();

$where = array(
  "product.category_id" => "= '$dmi_category_id'",
);
foreach ($values as $field => $value) {
  if (!$value) continue;
  $products += $object->seek($value, $where, 50);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("barcode", $barcode);
$smarty->assign("products", $products);
$smarty->display("inc_list_dmi_search.tpl");
