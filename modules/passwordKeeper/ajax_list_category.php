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

// Récupération de la liste des catégories
$category = new CPasswordCategory();
$category->password_keeper_id = $password_keeper_id;

$categories = $category->loadMatchingList();
foreach ($categories as $_category) {
  $_category->loadBackRefs("passwords", "password_description");
}

$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->assign("category"  , $category);
$smarty->display("inc_list_category.tpl");