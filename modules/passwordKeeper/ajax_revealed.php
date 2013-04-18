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

$password_id = CValue::getOrSession("password_id");

$password = new CPasswordEntry();
$password->load($password_id);

// Déchiffrement
$revealed = $password->getPassword();

$smarty = new CSmartyDP();
$smarty->assign("revealed", $revealed);
$smarty->display("inc_revealed.tpl");