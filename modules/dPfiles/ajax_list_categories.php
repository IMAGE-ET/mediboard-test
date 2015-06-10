<?php 

/**
 * $Id$
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$page               = intval(CValue::get('page'  , 0));
$filter             = CValue::getOrSession("filter", "");
$eligible_file_view = CValue::getOrSession("eligible_file_view");
$class              = CValue::getOrSession("class");

$step  = 25;
$order = "class, nom";

$where = array();
if ($eligible_file_view == "1") {
  $where["eligible_file_view"] = "= '1'";
}
if ($eligible_file_view == "0") {
  $where["eligible_file_view"] = "= '0'";
}

if ($class) {
  $where["class"] = "= '$class'";
}

$category = new CFilesCategory;
if ($filter) {
  $categories       = $category->seek($filter, $where, "$page, $step", true, null, $order);
  $total_categories = $category->_totalSeek;
}
else {
  $categories       = $category->loadList($where, $order, "$page, $step");
  $total_categories = $category->countList($where);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("categories"      , $categories);
$smarty->assign("total_categories", $total_categories);
$smarty->assign("page"             , $page);
$smarty->assign("step"             , $step);
$smarty->display("inc_list_categories.tpl");
