<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_class = CValue::get("object_class");
$keywords     = CValue::post("keywords_category");

$order      = "nom";
$categories = array();

$instance = new CFilesCategory;
$where = array($instance->_spec->ds->prepare("`class` IS NULL OR `class` = %", $object_class));
$categories = array_merge($categories, $instance->seek($keywords, $where, null, null, null, $order));

$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->assign("nodebug"   , true);
$smarty->assign("keywords"  , $keywords);
$smarty->display("inc_category_autocomplete.tpl");
?>