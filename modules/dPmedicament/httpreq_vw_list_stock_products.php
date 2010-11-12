<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$keywords     = CValue::getOrSession("keywords");
$letter       = CValue::getOrSession("letter", "");
$start = intval(CValue::getOrSession("start", 0));
$category_id  = CValue::getOrSession("category_id", CAppUI::conf('bcb CBcbProduitLivretTherapeutique product_category_id'));

if (!$keywords) {
  $keywords = "%";
}

$where = array(
  "code" => "IS NOT NULL",
  "name" => ($letter === "#" ? "RLIKE '^[^A-Z]'" : "LIKE '$letter%'"),
);

if ($category_id) {
  $where["category_id"] = "= '$category_id'";
}

$product = new CProduct;
$list_products = $product->seek($keywords, $where, "$start, 25", true);

foreach($list_products as $_product) {
  $_bcb_product = CBcbProduit::get($_product->code, false);
  $_bcb_product->isInLivret(false);
  $_product->_is_valid = $_bcb_product->libelle != "";
  $_product->_in_livret = $_bcb_product->inLivret ? 1 : 0;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_products", $list_products);
$smarty->assign("total", $product->_totalSeek);
$smarty->assign("start", $start);

$smarty->display("inc_vw_list_stock_products.tpl");
