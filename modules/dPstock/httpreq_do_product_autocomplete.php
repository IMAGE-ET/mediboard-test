<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$keywords    = CValue::post("_view");
$category_id = CValue::post("category_id");

$where = array();
if($category_id){
  $where["category_id"] = " = '$category_id'";
}
$product = new CProduct();
$matches = $product->seek($keywords, $where, 30, false, null, "name");
 
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_product_autocomplete.tpl");
