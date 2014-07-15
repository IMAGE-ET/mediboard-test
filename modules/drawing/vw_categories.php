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

CCanDo::checkAdmin();

$category = new CDrawingCategory();
/** @var CDrawingCategory[] $categories */
$categories = $category->loadList(null, "name ASC");

foreach ($categories as $_cat) {
  $_cat->loadRefsFiles();
}

// smarty
$smarty = new CSmartyDP();
$smarty->assign("category", $category);
$smarty->assign("categories", $categories);
$smarty->display("vw_categories.tpl");