<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CPasswordKeeper::checkHTTPS();

CCanDo::checkAdmin();

$password_id = CValue::getOrSession("password_id");
$category_id = CValue::getOrSession("category_id");

// Récupération de la catégorie
$category = new CPasswordCategory();
$category->load($category_id);
$category->loadRefsPasswords();

// Récupération du mot de passe
$password = new CPasswordEntry();
$password->load($password_id);

$smarty = new CSmartyDP();
$smarty->assign("category"   , $category);
$smarty->assign("password"   , $password);
$smarty->display("inc_edit_password.tpl");