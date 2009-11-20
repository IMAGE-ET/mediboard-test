<?php /* $Id: vw_operations.php 7351 2009-11-17 09:58:58Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: 7351 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsRead();

$check_list_id = CValue::get("check_list_id");

$check_list = new CDailyCheckList;
$check_list->load($check_list_id);
$check_list->loadItemTypes();
$check_list->loadRefsFwd();
$check_list->loadBackRefs('items');

$check_item_category = new CDailyCheckItemCategory;
$check_item_category->target_class = $check_list->object_class;
$check_item_category->type = $check_list->type;
$check_item_categories = $check_item_category->loadMatchingList("title");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("check_list"           , $check_list);
$smarty->assign("check_item_categories", $check_item_categories);
$smarty->display("inc_edit_check_list.tpl");
