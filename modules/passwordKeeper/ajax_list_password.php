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

$category_id = CValue::getOrSession("category_id");
$password_id = CValue::getOrSession("password_id");

$password = new CPasswordEntry();
$password->category_id = $category_id;

// Récupération de la liste des mots de passe
$passwords = $password->loadMatchingList();

$smarty = new CSmartyDP();
$smarty->assign("passwords"  , $passwords);
$smarty->assign("password"   , $password);
$smarty->assign("revealed"   , null);
$smarty->assign("password_id", $password_id);
$smarty->display("inc_list_password.tpl");