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

$category_id = CValue::get("category_id");

$category = new CExClassCategory();
$category->load($category_id);
$category->loadRefsNotes();

$smarty = new CSmartyDP();
$smarty->assign("category", $category);
$smarty->display("inc_edit_ex_class_category.tpl");