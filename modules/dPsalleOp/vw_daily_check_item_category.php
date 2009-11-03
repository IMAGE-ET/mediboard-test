<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$item_category_id = CValue::getOrSession('item_category_id');

$item_category = new CDailyCheckItemCategory;
$item_category->load($item_category_id);

$item_categories_list = $item_category->loadList(null, 'title');

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("item_category", $item_category);
$smarty->assign("item_categories_list", $item_categories_list);
$smarty->display("vw_daily_check_item_category.tpl");

?>