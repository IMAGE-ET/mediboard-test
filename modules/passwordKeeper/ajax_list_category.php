<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$password_keeper_id = CValue::getOrSession("password_keeper_id");
$category_id        = CValue::getOrSession("category_id");

// Récupération de la liste des catégories
$category = new CPasswordCategory();
$category->password_keeper_id = $password_keeper_id;

$categories = $category->loadMatchingList();
$counts     = array();
foreach ($categories as $_category) {
  $_category->loadBackRefs("passwords", "password_description");
  $counts[$_category->_id] = $_category->countBackRefs("passwords");
}

$smarty = new CSmartyDP();
$smarty->assign("categories" , $categories);
$smarty->assign("category"   , $category);
$smarty->assign("category_id", $category_id);
$smarty->assign("counts"     , $counts);
$smarty->display("inc_list_category.tpl");