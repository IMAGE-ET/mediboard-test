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

CCanDo::checkEdit();

$category_id = CValue::getOrSession("category_id");

$category = new CFilesCategory();
$category->load($category_id);
$category->countDocItems();
$category->loadRefsNotes();

$listClass = CApp::getChildClasses();

$classes = array();
foreach ($listClass as $key => $_class) {
  $classes[$_class] = CAppUI::tr($_class);
}
CMbArray::naturalSort($classes);

$smarty = new CSmartyDP();
$smarty->assign("category" , $category);
$smarty->assign("listClass", $classes );
$smarty->display("inc_form_category.tpl");