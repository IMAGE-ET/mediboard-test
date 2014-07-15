<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$cat_id = CValue::get("category_id");

$category = new CDrawingCategory();
$category->load($cat_id);
$category->loadRefsFiles();

$smarty = new CSmartyDP();
$smarty->assign("category", $category);
$smarty->display("inc_list_files_for_category.tpl");