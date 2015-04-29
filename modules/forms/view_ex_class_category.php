<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$category = new CExClassCategory();
$categories = $category->loadGroupList(null, "title");

$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->display("view_ex_class_category.tpl");
