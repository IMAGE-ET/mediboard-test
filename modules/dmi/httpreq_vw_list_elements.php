<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;
CCanDo::checkRead();

$object_class   = CValue::get("object_class", "CDMI");
$category_id    = CValue::get("category_id");
$start          = intval(CValue::get("start_$object_class", 0));
$keywords       = CValue::get("keywords_$object_class", "%");

CValue::setSession("keywords_$object_class", $keywords);

if (!$keywords) $keywords = "%";

$element = new $object_class;

$where = array();

// Chargement de tous les dmis
if ($category_id) {
  $where["category_id"] = "= '$category_id'";
}

$list_elements = $element->seek($keywords, $where, "$start,30", true, null, "nom");
$total = $element->_totalSeek;

foreach ($list_elements as $_element) {
	$_element->loadExtProduct();
	$_element->_ext_product->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_elements", $list_elements);
$smarty->assign("total", $total);
$smarty->assign("start", $start);
$smarty->assign("object_class", $object_class);
$smarty->display("inc_list_elements.tpl");
